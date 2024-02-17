import requests
import sys

# Get the url of the image from the command-line
url = sys.argv[1]
filename = sys.argv[2]
type = sys.argv[3]

# Get the image from the url
img = requests.get(url)

if type == 'movie':
    name = filename + '.jpg'

    # Save the image in the directory ../../img/Covers/movies/ with os module
    with open('../../img/Covers/movies/' + name, 'wb') as f:
        f.write(img.content)
elif type == 'serie':
    name = filename + '.jpg'

    # Save the image in the directory ../../img/Covers/series/ with os module
    with open('../../img/Covers/series/' + name, 'wb') as f:
        f.write(img.content)
else:
    print('Invalid type')
    sys.exit(1)  