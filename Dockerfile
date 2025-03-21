FROM php:8.1-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Set the working directory
WORKDIR /var/task

# Copy your project files to the container
COPY . /var/task

# Run the PHP server (you can change this according to your needs)
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
