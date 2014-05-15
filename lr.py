#!/usr/bin/python2
from livereload import Server, shell

server = Server()
server.watch("cdcsignin.html")
server.watch("cdcsignin.css")
server.watch("cdcsignin.js")
server.serve()
