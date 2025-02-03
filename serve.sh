#!/bin/bash

# Configuration
PORT=8000
HOST=localhost
PROJECT_DIR="."  # Current directory, change this if needed

# Function to start the PHP server
start_server() {
    php -S $HOST:$PORT -t $PROJECT_DIR
}

# Start the server
echo "Starting PHP server at http://$HOST:$PORT"
echo "Press Ctrl+C to stop the server"
start_server
