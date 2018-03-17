# Web Service for:
# - detect faces on image and return them with their 128D encoding using HOG
# - train a k-Nearest-Neighbors (KNN) model with the 128D encodings, learning the vectors of the faces
# - predict a known label by finding the k most similar faces (images with closet face-features under eucledian distance)
#   in its training set, and performing a majority vote (possibly weighted) on their label.

import math
import face_recognition
import base64
import os
import os.path
from io import BytesIO
from PIL import Image
from flask import Flask, jsonify, request, redirect, after_this_request
from sklearn import neighbors
import pickle
import numpy as np
import json


ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif'}
FOLDER_FACES = "faces"
MODEL_FILE = os.path.abspath("ws_trained.clf")
ASSOCIATION_FILE = os.path.abspath("associations.json")
CLASSIFIER = None
DISTANCE_THRESHOLD = 0.6


# Creates a new KNN Model from the provided data [{encoding, user_id}, {..}, ..]
def retrain(data):
    global CLASSIFIER
    x = []
    y = []

    for subject in data:
        x.append(subject['encoding'])
        y.append(subject['user_id'])

    train_len = len(x)
    n_neighbors = int(round(math.sqrt(train_len)))
    CLASSIFIER = neighbors.KNeighborsClassifier(n_neighbors=n_neighbors, algorithm='ball_tree', weights='distance')

    if train_len > 0:
        CLASSIFIER.fit(x, y)

    with open(MODEL_FILE, 'wb') as fw:
        pickle.dump(CLASSIFIER, fw)


# Reads trained associations for recovering purposes.
def read_associations():
    with open(os.path.abspath(ASSOCIATION_FILE), 'r') as afr:
        return json.load(afr)


def write_associations(data):
    with open(ASSOCIATION_FILE, 'w') as afw:
        json.dump(data, afw)


if not os.path.isfile(ASSOCIATION_FILE):
    write_associations([])

if os.path.isfile(MODEL_FILE):
    with open(os.path.abspath(MODEL_FILE), 'rb') as fr:
        CLASSIFIER = pickle.load(fr)
else:
    retrain(read_associations())

app = Flask(__name__)


def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS


# Called when registering an user image
@app.route('/detect', methods=['POST'])
def upload_image():
    file = request.files['file']

    if file.filename == '':
        return redirect(request.url)

    if file and allowed_file(file.filename):
        faces = extract_faces_from_image(file.filename, file)
        json_faces = jsonify(base64_faces(faces))

        @after_this_request
        def add_header(response):
            response.headers['Content-Type'] = 'application/json; charset=utf-8'
            return response

        return json_faces


# Called when rebuild of the model is required.
@app.route('/retrain', methods=['POST'])
def retrain_faces():
    retrain(request.get_json(force=True))
    return "trained"


# Called when only one new face is trained.
@app.route('/train', methods=['POST'])
def train_face():
    train(request.get_json(force=True))
    return "trained"


def train(face_data):
    associations = read_associations()

    associations.append(face_data)

    retrain(associations)

    write_associations(associations)


# Called for classification (prediction) of a face encoding
@app.route('/recognize', methods=['POST'])
def recognize():
    if CLASSIFIER is None:
        return 'Error: Model is not trained yet.'

    return recognize_faces([request.get_json(force=True)])


def recognize_faces(faces_encodings):
    closest_distances = CLASSIFIER.kneighbors(faces_encodings, n_neighbors=1)
    are_matches = [closest_distances[0][0][0] <= DISTANCE_THRESHOLD]

    # Predict classes and remove classifications that aren't within the threshold
    predictions = [pred if rec else "unknown" for pred, rec in zip(CLASSIFIER.predict(faces_encodings), are_matches)]

    print("Preditcted: {}".format(predictions[0]))

    return str(predictions[0])


def extract_faces_from_image(file_name, file_stream):
    image = face_recognition.load_image_file(file_stream)
    face_locations = face_recognition.face_locations(image)
    print("I found {} face(s) in this photograph.".format(len(face_locations)))

    faces = {}

    for face_location in face_locations:
        top, right, bottom, left = face_location
        print("A face is located at pixel location Top: {}, Left: {}, Bottom: {}, Right: {}".format(top, left, bottom, right))

        face_image = image[top:bottom, left:right]
        pil_image = Image.fromarray(face_image)

        face_image_name = "%d_%d_%d_%d_%s" % (top, right, bottom, left, file_name)
        faces[face_image_name] = pil_image

    return faces


def get_face_encoding(image):
    width, height = image.size

    return face_recognition.face_encodings(np.array(image), known_face_locations=[[0, height, width, 0]])[0]


# TODO move encoding extraction out of here!
def base64_faces(faces):
    base64_encoded_faces = {}
    buffered = BytesIO()

    for name, image in faces.items():
        image.save(buffered, format="JPEG")
        base64_encoded_faces[name] = {
            'image': base64.b64encode(buffered.getvalue()).decode('utf-8'),
            'encoding': get_face_encoding(image).tolist()
        }

    return base64_encoded_faces


if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5004, debug=True)
