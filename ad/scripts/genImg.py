import requests
import sys
import os


# Get the url of the image from the command-line
url = sys.argv[1]
filename = sys.argv[2]
type = sys.argv[3]

print(url)

# Get the image from the url
img = requests.get(url)

if type == 'movie':
    name = filename + '.jpg'

    # Save the image in the directory ../../img/Covers/movies/ with os module
    with open('../../img/Covers/movies/' + name, 'wb') as f:
        f.write(img.content)



    
