import cv2
import numpy as np
import sys
import os

user_image_path = sys.argv[1]      # image uploadée par le user
ref_folder_path = sys.argv[2]      #  images a comparer

# verification
if not os.path.exists(user_image_path):
    print(f"ERROR_USER_IMAGE_NOT_FOUND:{user_image_path}")
    sys.exit(1)

if not os.path.exists(ref_folder_path):
    print(f"ERROR_REF_FOLDER_NOT_FOUND:{ref_folder_path}")
    sys.exit(1)

# Lecture image utilisateur
user_img = cv2.imread(user_image_path)
if user_img is None:
    print(f"ERROR_CANNOT_READ_USER_IMAGE:{user_image_path}")
    sys.exit(1)

user_gray = cv2.cvtColor(user_img, cv2.COLOR_BGR2GRAY)

# fonction mse
def mse(img1, img2):
    diff = img1.astype("float") - img2.astype("float")
    return np.mean(diff ** 2)

# Parcours toutes les images du dossier
best_score = None
best_image = None

for filename in os.listdir(ref_folder_path):
    ref_path = os.path.join(ref_folder_path, filename)

    #  ignore si ce n’est pas un fichier
    if not os.path.isfile(ref_path):
        continue

    # lecture image
    ref_img = cv2.imread(ref_path)
    if ref_img is None:
        print(f"WARNING: impossible de lire {ref_path}")
        continue

    ref_gray = cv2.cvtColor(ref_img, cv2.COLOR_BGR2GRAY)
    ref_gray = cv2.resize(ref_gray, (user_gray.shape[1], user_gray.shape[0]))

    score = mse(user_gray, ref_gray)

    print(f"Comparing {filename} -> MSE: {score}",  flush=True);

    if best_score is None or score < best_score:
        best_score = score
        best_image = filename

# Affiche la best correspondance
print(f"BEST_IMAGE:{best_image}",  flush=True)
print(f"MSE_SCORE:{best_score}",  flush=True)
