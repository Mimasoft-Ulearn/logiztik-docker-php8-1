-- Crear un usuario administrador distinto del root
CREATE DATABASE IF NOT EXISTS logiztik_fc;
CREATE DATABASE IF NOT EXISTS logiztik_sistema;

-- Otorgar privilegios administrativos al usuario admin

-- -- Deshabilitar el acceso remoto para el usuario root
-- UPDATE mysql.user SET Host='localhost' WHERE User='root' AND Host='%';

-- Crear usuario de aplicación

GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;
CREATE USER 'logiztik'@'%' IDENTIFIED BY 'ziZK5WtSpwu#7&';

-- Crear bases de datos

-- Otorgar permisos al usuario de aplicación
GRANT ALL PRIVILEGES ON logiztik_fc.* TO 'logiztik'@'%';
GRANT ALL PRIVILEGES ON logiztik_sistema.* TO 'logiztik'@'%';

-- Aplicar cambios
FLUSH PRIVILEGES;
