# Proyecto Backend de Enfermeros – Symfony API

## Descripción del Proyecto
Este proyecto consiste en el desarrollo de un backend en Symfony que expone una API para la gestión de enfermeros.  
Incluye operaciones CRUD, conexión a base de datos mediante Doctrine, pruebas unitarias, integración continua con GitHub Actions y documentación técnica.  
El objetivo es comprender el funcionamiento de un backend profesional utilizando PHP y Symfony.

---

## Tecnologías Utilizadas
- Symfony
- PHP
- Composer
- MySQL / MariaDB
- Doctrine ORM
- PHPUnit
- Git y GitHub Actions

---

## Instalación y Configuración
El proyecto requiere clonar el repositorio, instalar las dependencias necesarias y configurar el archivo de entorno para establecer la conexión con la base de datos.  
Posteriormente se debe crear la base de datos y aplicar las migraciones generadas por Doctrine para preparar el esquema.

---

## Uso del Proyecto
Symfony permite ejecutar un servidor de desarrollo para acceder a la API localmente.  
Una vez en funcionamiento, la API ofrece las funcionalidades necesarias para gestionar enfermeros, realizar validaciones y obtener información desde la base de datos.

---

## Pruebas Unitarias
El proyecto incorpora pruebas unitarias mediante PHPUnit, centradas en las funciones principales del controlador y la lógica de negocio.  
Estas pruebas verifican tanto resultados correctos como errores previstos, garantizando un funcionamiento estable y predecible.

---

## Integración Continua (CI)
Se ha configurado un flujo de trabajo en GitHub Actions que ejecuta automáticamente las pruebas unitarias cada vez que se realizan cambios en el repositorio.  
Esto asegura que el código mantenga su calidad y permite detectar errores de manera temprana durante el desarrollo.

---

## Base de Datos

### Diseño
El modelo lógico de la base de datos fue diseñado utilizando MySQL Workbench, definiendo la estructura necesaria para almacenar y gestionar enfermeros.

### Implementación con Doctrine
Una vez configurado Doctrine, se generaron las entidades y migraciones para sincronizar el diseño con Symfony.  
El resultado fue comparado con el modelo inicial para asegurar que la estructura fuera la esperada.

### Pruebas en entorno local y remoto
El proyecto fue probado tanto con una base de datos local como con una base de datos centralizada alojada en un servidor externo.  
De esta manera, se validó su funcionamiento en diferentes entornos.

---

## Documentación Técnica
Se ha elaborado documentación detallada que incluye:

- El diseño final del modelo de datos  
- Explicación completa del CRUD implementado  
- Validaciones realizadas mediante Postman  
- Evidencias del funcionamiento del pipeline de integración continua  
- Comparación entre el modelo teórico y el generado por Doctrine  
- Uso del repositorio y gestión de incidencias  

---

## Enlace al Repositorio
[https://github.com/AdrianPalmadev/Proyecto-DAW2
](https://github.com/AdrianPalmadev/Proyecto-DAW2)

---

## Autor
**Adrián Palma**

Proyecto desarrollado de forma individual.
