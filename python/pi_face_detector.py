# This is a demo of running face recognition on a Raspberry Pi.
# This program will print out the names of anyone it recognizes to the console.

# To run this, you need a Raspberry Pi 2 (or greater) with face_recognition and
# the picamera[array] module installed.
# You can follow this installation instructions to get your RPi set up:
# https://gist.github.com/ageitgey/1ac8dbe8572f3f533df6269dab35df65

import face_recognition
import picamera
import numpy as np
import cv2
import datetime
import time
import base64
import requests
from PIL import Image
from io import BytesIO

MY_RESOURCE_ID                    = 4
ROUTE_GET_RESERVATION_IN_PROGRESS = 'http://s4k.co/api/pi/reservations'
ROUTE_CHECK_ACCESS                = 'http://s4k.co/api/pi/check-access'

def current_date_for_filename():
    now      = datetime.datetime.now()
    nowAsStr = now.strftime("%Y-%m-%d_%H-%M-%S")
    return nowAsStr

def get_face_locations_from_image(image):
    face_locations = face_recognition.face_locations(image)
    print("Found {} faces in image.".format(len(face_locations)))
    return face_locations

def save_detected_faces_to_files(image, face_locations, detectiontime, face_encodings):

    saveFaces = True
    print("Detection Time %s" % (detectiontime))
    base_path                = '/home/pi/Desktop/onit1/'
    original_filename_format = '/home/pi/Desktop/onit1/image_cv2_%s_original.jpg'
    face_filename_format     = '/home/pi/Desktop/onit1/image_cv2_%s_%s.jpg'
    
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

def crate_faces_for_base64(image, face_locations, face_encodings):
    faces = []
    for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
        face_image = image[top:bottom, left:right]
        pil_image = Image.fromarray(face_image)
        faces.append(pil_image)

    return faces

def base64_faces(faces):
    bfaces = []
    buffered = BytesIO()
    
    for image in faces:
        #############################################################.items():
        image.save(buffered, format="JPEG")
        one_face_base64 = base64.b64encode(buffered.getvalue()).decode('utf-8')

        print("Base64: %s" % one_face_base64)
                                           
        bfaces.append(one_face_base64)

    return bfaces



def cameraDestruct():
    print('Stopping camera')
    camera.stop_preview()

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
    r = requests.get(url, params=payload)
    print('CALLING url: with POST: %s' % r.url)
    return r.json()

def getReservationInProgress():
    payload = {'resource_id': MY_RESOURCE_ID}
    r       = httpGet(ROUTE_GET_RESERVATION_IN_PROGRESS, payload)
    #print('%s' % (r))

    if (len(r['result']) == 0):
        print("------------------- SLEEEEEEEP ---------------")
        return False
    
    return True

def sendEncoding(enc):
    payload = {'encoding': enc}
    r       = requests.post(ROUTE_TEST_POST, payload)
    print('%s' % (r))
    
    ## TODO parse response
    
    return True


# Get a reference to the Raspberry Pi camera.
# If this fails, make sure you have a camera connected to the RPi and that you
# enabled your camera in raspi-config and rebooted first.
camera            =  picamera.PiCamera()
camera.rotation   = 180
camera.resolution = (320, 240)
#camera.start_preview()
full_image = np.empty((240, 320, 3), dtype=np.uint8)

# Initialize some variables
face_locations = []
face_encodings = []

def main():

    # init
    eyeIsOpen = False
    
    # real main loop
    while True:

        eyeIsOpen = getReservationInProgress()

        if (eyeIsOpen == False):
            print("Nothing to do... Sleep")
            time.sleep(10)
            continue
                          
        print("Capturing image...")
        
        camera.capture(full_image, format="rgb")
        #camera.capture(full_image, format="bgr")
        
        # Find all the faces and face encodings in the current frame of video
        face_locations = get_face_locations_from_image(full_image)

        if (len(face_locations) == 0):
            continue
        
        # LATER
        face_encodings = face_recognition.face_encodings(full_image, face_locations)

        detectiontime = current_date_for_filename()

        #Save can be optional
                
        save_detected_faces_to_files(full_image, face_locations, detectiontime, face_encodings)

        #Collect the small faces only
        faces = crate_faces_for_base64(full_image, face_locations, face_encodings)

        #Base64 them
        faces_b64_encoded = base64_faces(faces)
                          
        #face_filecontents = []

#while True:

#    if (recording == False):
#        print("Nothing to do... Sleep")
##        continue
##                      
##    print("Capturing image.")
##    # Grab a single frame from the RPi camera as a numpy array
##    #camera.capture(output, format="rgb")
##    camera.capture(full_image, format="bgr")
##    
##    # Find all the faces and face encodings in the current frame of video
##    face_locations = get_face_locations_from_image(full_image)
##
##    # LATER
##    face_encodings = face_recognition.face_encodings(output, face_locations)
##
##    if (len(face_locations) == 0):
##        continue
##    
##    detectiontime = current_date_for_filename()
##
##    #Save can be optional
##    save_detected_faces_to_files(full_image, face_locations, detectiontime)
##
##    #Collect the small faces only
##    faces = crate_faces_for_base64(full_image, face_locations, face_encodings)
##
##    #Base64 them
##    faces_b64_encoded = base64_faces(faces)
##
##
##                      
##    #originalfilename = ('/home/pi/Desktop/onit1/image_cv2_%s_original.jpg' % (detectiontime))
##    #cv2.imwrite(originalfilename, output)
##
##    face_filecontents = []
##    
    #for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
    #for (top, right, bottom, left) in zip(face_locations):
        #print("(%s,%s)(%s,%s)" % (top, left, bottom,right))
        #print("Detection Time %s" % (detectiontime))
        #camera.capture('/home/pi/Desktop/onit1/image_%s.jpg' % (counter));

        #face_image = output[top:bottom, left:right]

        #facefilename = ('/home/pi/Desktop/onit1/image_cv2_%s_%s.jpg' % (detectiontime, counter))
        #cv2.imwrite(facefilename, face_image)
        #counter = counter+1

        #jpgimage = cv2.imread(facefilename, 0)

        #with open(facefilename, "rb") as facefilesaved:
        #    base64ed = base64.b64encode(facefilesaved.read())
        #    face_filecontents.append(base64ed)

    #for basec in face_filecontents:
    #    print("Base64 %s" % basec)

           
    # Loop over each face found in the frame to see if it's someone we know.
#    for face_encoding in face_encodings:
#        print("Encoding of faces: %s" % (face_encoding[0].tolist()))


        # See if the face is a match for the known face(s) 
        #match = face_recognition.compare_faces([obama_face_encoding], face_encoding)
        #name = "<Unknown Person>"
        #if match[0]:
        #    name = "Barack Obama"
        #print("I see someone named {}!".format(name))


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
    #GPIO.cleanup()
