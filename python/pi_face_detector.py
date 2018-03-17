import face_recognition
import picamera
import numpy as np
import cv2
#HELPER
import datetime
import time
import base64
#HTTP
import requests
import json
#LCD
import smbus
import RPi.GPIO as GPIO

#from flask import jsonify
from PIL import Image
from io import BytesIO
import socket

GPIO.setmode(GPIO.BOARD)

MY_RESOURCE_ID                    = 4
ROUTE_GET_RESERVATION_IN_PROGRESS = 'http://s4k.co/api/pi/reservations'
ROUTE_POST_RECOGNIZE              = 'http://s4k.co/api/pi/recognize'

## sudo i2cdetect -y 1

#######################################################################
## HELPER STUFF
#######################################################################
def current_date_for_filename():
    now      = datetime.datetime.now()
    nowAsStr = now.strftime("%Y-%m-%d_%H-%M-%S")
    return nowAsStr

#######################################################################
## FACE STUFF
#######################################################################
def get_face_locations_from_image(image):
    face_locations = face_recognition.face_locations(image)
    print("Found {} faces in image.".format(len(face_locations)))
    return face_locations

def save_detected_faces_to_files(image, face_locations, detectiontime, face_encodings):
    saveFaces = True
    print("Detection Time %s" % (detectiontime))
    base_path                = '/home/pi/Desktop/onit1/'
    original_filename_format = base_path + 'image_cv2_%s_original.jpg'
    face_filename_format     = base_path + 'image_cv2_%s_%s.jpg'
    
    originalfilename = (original_filename_format % (detectiontime))
    #cv2.cvtColor(image, image, COLOR_RGB2BGR)
    cv2.imwrite(originalfilename, image)

    if (saveFaces == False):
        return

    counter = 0

    for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
        print("(%s,%s)(%s,%s)" % (top, left, bottom,right))
        face_image = image[top:bottom, left:right]
        facefilename = (face_filename_format % (detectiontime, counter))
        #cv2.cvtColor(face_image, face_image, COLOR_RGB2BGR)
        cv2.imwrite(facefilename, face_image)
        counter = counter+1

#######################################################################
## HTTP STUFF
#######################################################################    
## url:     string
## payload: {'key_1': 'value1', 'key_2': ['value2', 'value3']}
def httpGet(url, payload):
    r = requests.get(url, params=payload)
    print('CALLING url: with GET: %s' % r.url)
    return r.json()

def httpPost(url, payload):
    r = requests.post(url, json=payload)
    print('CALLING url: %s' % r.url)
    print('Result: %s' % r.text)
    return r.json()

#######################################################################
## API REPOSITORY STUFF
#######################################################################  
def getReservationInProgress():
    payload = {'resource_id': MY_RESOURCE_ID}
    r       = httpGet(ROUTE_GET_RESERVATION_IN_PROGRESS, payload)
    #print('%s' % (r))

    if (len(r['result']) == 0):
        print("[RESERVATION] No reservation going to sleep.")
        return False

    print("[RESERVATION] Should be take photos.")
    return True

##def set_default(obj):
##    if isinstance(obj, set):
##        return list(obj)
##    raise TypeError

def sendEncoding(enc):
##    payload = {'face_encodings': json.dumps(enc, default=set_default)}
    payload = {'face_encodings': enc, 'resource_id': MY_RESOURCE_ID}
    r       = httpPost(ROUTE_POST_RECOGNIZE, payload)
    print(r)
    return r['result'];

#######################################################################
# Camera STUFF
####################################################################### 
def cameraInit():
    global camera
    camera            =  picamera.PiCamera()
    camera.rotation   = 180
    camera.resolution = (320, 240)
    camera.hflip      = True
    camera.sharpness  = 10
    camera.start_preview()
    
def cameraDestruct():
    print('Stopping camera')
    global camera
    camera.stop_preview()

######################################################################
## LCD STUFF
######################################################################
I2C_ADDR  = 0x27 # I2C device address
LCD_WIDTH = 16   # Maximum characters per line
# Define some device constants
LCD_CHR = 1 # Mode - Sending data
LCD_CMD = 0 # Mode - Sending command
LCD_LINE_1 = 0x80 # LCD RAM address for the 1st line
LCD_LINE_2 = 0xC0 # LCD RAM address for the 2nd line
LCD_LINE_3 = 0x94 # LCD RAM address for the 3rd line
LCD_LINE_4 = 0xD4 # LCD RAM address for the 4th line
LCD_BACKLIGHT  = 0x08  # On
#LCD_BACKLIGHT = 0x00  # Off
ENABLE = 0b00000100 # Enable bit
# Timing constants
#E_PULSE = 0.0005
E_PULSE = 0.0001
E_DELAY = 0.0005
#Open I2C interface
#bus = smbus.SMBus(0)  # Rev 1 Pi uses 0
bus = smbus.SMBus(1) # Rev 2 Pi uses 1

def lcd_init():
  lcd_byte(0x33,LCD_CMD) # 110011 Initialise
  lcd_byte(0x32,LCD_CMD) # 110010 Initialise
  lcd_byte(0x06,LCD_CMD) # 000110 Cursor move direction
  lcd_byte(0x0C,LCD_CMD) # 001100 Display On,Cursor Off, Blink Off 
  lcd_byte(0x28,LCD_CMD) # 101000 Data length, number of lines, font size
  lcd_byte(0x01,LCD_CMD) # 000001 Clear display
  time.sleep(E_DELAY)

def lcd_byte(bits, mode):

  bits_high = mode | (bits & 0xF0) | LCD_BACKLIGHT
  bits_low  = mode | ((bits<<4) & 0xF0) | LCD_BACKLIGHT

  # High bits
  bus.write_byte(I2C_ADDR, bits_high)
  lcd_toggle_enable(bits_high)

  # Low bits
  bus.write_byte(I2C_ADDR, bits_low)
  lcd_toggle_enable(bits_low)

def lcd_toggle_enable(bits):
  # Toggle enable
  time.sleep(E_DELAY)
  bus.write_byte(I2C_ADDR, (bits | ENABLE))
  time.sleep(E_PULSE)
  bus.write_byte(I2C_ADDR,(bits & ~ENABLE))
  time.sleep(E_DELAY)

def lcd_string(message,line):
  message = message.ljust(LCD_WIDTH," ")
  lcd_byte(line, LCD_CMD)
  for i in range(LCD_WIDTH):
    lcd_byte(ord(message[i]),LCD_CHR)

######################################################################
    
global camera
camera = 0

#camera            =  picamera.PiCamera()
#camera.rotation   = 180
#camera.resolution = (320, 240)
#camera.start_preview()
full_image = np.empty((240, 320, 3), dtype=np.uint8)
   
face_locations = []
face_encodings = []

def main():
    print("OnIt System Started...")
    
    # init
    eyeIsOpen = False
    in_use    = False
    cameraInit()
    lcd_init()

    lcd_string("  GoC18  ON IT",LCD_LINE_1)
    
    # real main loop
    while True:

        if (in_use == True):
            ## do in use stuff
            print("[IN USE] Starting to use.")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            time.sleep(2)
            lcd_string("In Use", LCD_LINE_1)
            lcd_string(" ", LCD_LINE_2)
            print("[IN USE] Resource is in use.")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            time.sleep(2)
            print("[IN USE] Resource is still in use.")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            time.sleep(2)
            print("[IN USE] Resource is in use now")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            print("[IN USE] .")
            time.sleep(2)
            print("[IN USE] Ended. Became free")
            in_use = False
            continue

        eyeIsOpen = getReservationInProgress()

        if (eyeIsOpen == False):
            print("Nothing to do... Sleep")
            lcd_string("FREE Register or",LCD_LINE_1)
            lcd_string("   reserve first",LCD_LINE_2)
            time.sleep(10)
            continue
                          
        print("Capturing image...")

        lcd_string("Reserved", LCD_LINE_1)
        lcd_string("Look into cam", LCD_LINE_2)
        
        camera.capture(full_image, format="rgb")
        #camera.capture(full_image, format="bgr")
        
        # Find all the faces and face encodings in the current frame of video
        face_locations = get_face_locations_from_image(full_image)

        if (len(face_locations) == 0):
            continue
        
        face_encodings = face_recognition.face_encodings(full_image, face_locations)

        detectiontime = current_date_for_filename()

        save_detected_faces_to_files(full_image, face_locations, detectiontime, face_encodings)

        ##### SEND face_encoding..........
        recogresult = sendEncoding(np.array(face_encodings).tolist())

        if (recogresult == True):
            print('[RECOGNITION] (/) Recogized')
            lcd_string("Reserved", LCD_LINE_1)
            lcd_string("Recognized   (/)", LCD_LINE_2)
            in_use = True
            
        else:
            print('[RECOGNITION] (x) Unrecognized')
            lcd_string("Reserved", LCD_LINE_1)
            lcd_string("Unrecognized (x)", LCD_LINE_2)
            time.sleep(2)
            lcd_string("Look into cam", LCD_LINE_2)
      

#---------------------------------------------------------------------
if __name__ == '__main__':

  try:
    main()
  except KeyboardInterrupt:
    #lcd_string("Bye.",LCD_LINE_1)
    #time.sleep(0.3)
    #lcd_string("Bye..",LCD_LINE_1)
    #time.sleep(0.3)
    #lcd_string("Bye...",LCD_LINE_1)
    #time.sleep(0.3)
    #lcd_byte(0x01, LCD_CMD)
    pass
  finally:
    #lcd_byte(0x01, LCD_CMD)
    cameraDestruct()
    lcd_string("  GoC18  ON IT",LCD_LINE_1)
    lcd_string(" ",LCD_LINE_2)
    #GPIO.cleanup()
