#!/bin/bash

# Read current version
current_version=$(cat VERSION)

# Increment patch version
version_parts=(${current_version//./ })
((version_parts[2]++))
new_version="${version_parts[0]}.${version_parts[1]}.${version_parts[2]}"

# Update version file
echo "$new_version" > VERSION

# Build and push with version tag
docker build -t verdonarthur/robot-in-black:"$new_version" .
docker push verdonarthur/robot-in-black:"$new_version"

# Also keep the latest tag
docker build -t verdonarthur/robot-in-black:latest .
docker push verdonarthur/robot-in-black:latest
