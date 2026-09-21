-- Versix: esquema de instalación limpia para la edición de portfolio.
-- Importar en una base vacía. No borra ni migra la base original del TFG.
SET NAMES utf8mb4;
CREATE TABLE artista (
 idartista INT PRIMARY KEY AUTO_INCREMENT, nombre VARCHAR(120) NOT NULL, imagen VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE album (
 idalbum INT PRIMARY KEY AUTO_INCREMENT, nombre VARCHAR(120) NOT NULL, imagen VARCHAR(255) NOT NULL,
 idartista INT NOT NULL, FOREIGN KEY (idartista) REFERENCES artista(idartista)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE estilo (
 idestilo INT PRIMARY KEY AUTO_INCREMENT, nombre VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE cancion (
 idcancion INT PRIMARY KEY AUTO_INCREMENT, nombre VARCHAR(160) NOT NULL,
 imagen VARCHAR(255) NOT NULL, audio VARCHAR(255) NOT NULL,
 idartista INT NOT NULL, idalbum INT NOT NULL, estilo_idestilo INT NOT NULL,
 FOREIGN KEY (idartista) REFERENCES artista(idartista), FOREIGN KEY (idalbum) REFERENCES album(idalbum),
 FOREIGN KEY (estilo_idestilo) REFERENCES estilo(idestilo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE biblioteca (idbiblioteca INT PRIMARY KEY AUTO_INCREMENT)
 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE usuario (
 idusuario INT PRIMARY KEY AUTO_INCREMENT, usuario VARCHAR(40) NOT NULL UNIQUE,
 email VARCHAR(254) NOT NULL UNIQUE, contrasena VARCHAR(255) NOT NULL,
 fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 idbiblioteca INT NOT NULL UNIQUE, FOREIGN KEY (idbiblioteca) REFERENCES biblioteca(idbiblioteca)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE playlist (
 idplaylist INT PRIMARY KEY AUTO_INCREMENT, nombre VARCHAR(80) NOT NULL,
 imagen VARCHAR(255) NOT NULL, idusuario INT NOT NULL,
 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE biblioteca_tiene_cancion (
 idcancion INT NOT NULL, idbiblioteca INT NOT NULL, PRIMARY KEY (idcancion, idbiblioteca),
 FOREIGN KEY (idcancion) REFERENCES cancion(idcancion) ON DELETE CASCADE,
 FOREIGN KEY (idbiblioteca) REFERENCES biblioteca(idbiblioteca) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE cancion_playlist (
 idcancion INT NOT NULL, idplaylist INT NOT NULL, PRIMARY KEY (idcancion, idplaylist),
 FOREIGN KEY (idcancion) REFERENCES cancion(idcancion) ON DELETE CASCADE,
 FOREIGN KEY (idplaylist) REFERENCES playlist(idplaylist) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE artista_tiene_cancion (
 idartista INT NOT NULL, idcancion INT NOT NULL, PRIMARY KEY (idartista, idcancion),
 FOREIGN KEY (idartista) REFERENCES artista(idartista), FOREIGN KEY (idcancion) REFERENCES cancion(idcancion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
