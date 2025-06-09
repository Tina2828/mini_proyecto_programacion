-- This SQL script creates a table for managing tasks with various attributes.
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO categories (name) VALUES
('Personal'),
('Trabajo'),
('Estudio'),
('Hogar'),
('Salud');

-- SQL script to create the 'tareas' table with specified columns and constraints
CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATETIME,
    category_id INT NOT NULL,
    priority ENUM('baja', 'media', 'alta') DEFAULT 'baja',
    status ENUM('pendiente', 'proceso', 'finalizada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Insert sample data into the 'tareas' table
INSERT INTO tareas (title, description, due_date, category_id, priority, status)
VALUES
('Comprar víveres', 'Comprar frutas y verduras para la semana', '2023-10-15 10:00:00', 1, 'media', 'pendiente'),
('Reunión de trabajo', 'Reunión con el equipo de desarrollo', '2023-10-16 14:00:00', 2, 'alta', 'pendiente'),
('Estudiar matemáticas', 'Repasar el capítulo 5 del libro de texto', '2023-10-17 18:00:00', 3, 'baja', 'pendiente'),
('Limpieza de la casa', 'Limpiar la sala y la cocina', '2023-10-18 09:00:00', 4, 'media', 'pendiente'),
('Cita médica', 'Consulta con el médico general', '2023-10-19 11:30:00', 5, 'alta', 'pendiente');
