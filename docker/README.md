#Per creare l'immagine docker

docker build -t registro

#Per lanciare l'immagine docker come daemon

docker run -d -p 80:80 registro


