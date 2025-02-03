#!/bin/bash

# Install required packages
sudo apt update
sudo apt install -y php php-cli inotify-tools

# Create a script to run PHP server and watch for changes
cat > run-dev-server.sh << 'EOL'
#!/bin/bash

# Configuration
PORT=8000
HOST=localhost
ROOT_DIR="."  # Change this to your project root directory

# Kill any existing PHP servers
pkill -f "php -S"

# Start PHP server in background
php -S $HOST:$PORT -t $ROOT_DIR &
PHP_SERVER_PID=$!

echo "PHP Development server started at http://$HOST:$PORT"
echo "Watching for file changes..."

# Watch for file changes
inotifywait -m -r -e modify,create,delete,move $ROOT_DIR |
while read -r directory events filename; do
    if [[ "$filename" =~ \.(php|html|css|js)$ ]]; then
        echo "File changed: $directory$filename"
        # Optional: Add any additional commands here that you want to run on file change
    fi
done

# Cleanup on script termination
trap "kill $PHP_SERVER_PID" EXIT
EOL

