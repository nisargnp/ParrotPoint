#!/bin/bash

# Setup Websocket server (follow README in websocket_server/) before running this script

echo "Make sure XAMPP is running and can access this folder"
echo "Starting Websocket Server"

php websocket_server/bin/server.php

if [[ $? -ne 0 ]];
then
	echo "FAILED starting websocket server"
fi