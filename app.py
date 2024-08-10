import os
from flask import Flask, request, render_template, send_from_directory, url_for
from werkzeug.utils import secure_filename
import tensorflow as tf
from tensorflow.keras.preprocessing import image
from PIL import Image
import numpy as np

app = Flask(__name__)
UPLOAD_FOLDER = 'uploads'
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

classes = ['Colon Adenocarcinoma', 'Colon Benign Tissue']
model_path = 'best_model2.h5'

# Load and compile the model
loaded_model = tf.keras.models.load_model(model_path, compile=False)
loaded_model.compile(optimizer=tf.keras.optimizers.Adamax(learning_rate=0.001), loss='binary_crossentropy', metrics=['accuracy'])

@app.route('/')
def home():
    return render_template('welcome.html')

@app.route('/predict', methods=['POST'])
def predict():
    if 'file' not in request.files:
        return 'No file part'
    file = request.files['file']
    if file.filename == '':
        return 'No selected file'
    if file:
        filename = secure_filename(file.filename)
        file_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
        file.save(file_path)
        
        # Preprocess the image
        img = Image.open(file_path)
        img = img.resize((300, 300))
        img_array = tf.keras.preprocessing.image.img_to_array(img)
        img_array = np.expand_dims(img_array, axis=0)
        img_array = img_array / 255.0
        
        # Make prediction
        predictions = loaded_model.predict(img_array)
        predicted_class_idx = int(predictions[0] > 0.5)  # 0 or 1
        predicted_class_label = classes[predicted_class_idx]

        # Return the rendered template with the image URL and prediction
        return render_template('welcome.html', prediction=predicted_class_label, image_url=url_for('uploaded_file', filename=filename))


@app.route('/uploads/<filename>')
def uploaded_file(filename):
    return send_from_directory(app.config['UPLOAD_FOLDER'], filename)


if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

if __name__ == '__main__':
    app.run(debug=True)
