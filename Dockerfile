# Dockerfile for SIPANDU (Express.js)
FROM node:18

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application code
COPY . .

# Expose port 8080
EXPOSE 8080

# Set environment variable for PORT
ENV PORT=8080

# Start the app
CMD ["node", "index.js"]
