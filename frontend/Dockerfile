FROM node:18-alpine

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy application files
COPY . .

EXPOSE 5173

# Start Vite dev server accessible from outside container
CMD ["npm", "run", "dev", "--", "--host"]
