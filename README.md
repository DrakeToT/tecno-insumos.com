# tecno-insumos.com
Sistema de Gestión de Inventario

# Guía de trabajo con ramas de características (`feature/`) en Git

Este documento describe el flujo recomendado para implementar nuevas funcionalidades en el proyecto utilizando la rama `develop` como base y ramas de características (`feature/`) para cada cambio.

---

## 1. Crear una rama de funcionalidad desde `develop`

Antes de comenzar cualquier cambio, asegurate de estar en la rama `develop` y traer los últimos cambios:

```bash
git checkout develop
git pull origin develop
git checkout -b feature/nombre-de-la-funcionalidad
```
Ejemplos de nombres: feature/login-modal, feature/validacion-formulario, feature/api-perfil

## 2. Realizar cambios en tu rama:

Agregá nuevos archivos o modificá los existentes.
Probá localmente que todo funcione correctamente.
Mantené tus commits organizados y con mensajes descriptivos.

## 3. Registrar los cambios
```bash
git add .
git commit -m "Descripción clara del cambio realizado"
```
Ejemplo: "Agregar validación de email en login.js"

## 4. Subir la rama al repositorio remoto
```bash
git push origin feature/nombre-de-la-funcionalidad
```
## 5. Crear un Pull Request (PR) en GitHub

Ir al repositorio en GitHub
Pestaña Pull requests → New pull request
Base: develop, Compare: feature/nombre-de-la-funcionalidad
Revisar los cambios y crear el PR

## 6. Revisar y aprobar el PR

Otro colaborador (o vos mismo si trabajás solo) revisa el PR
Se aprueba y se hace Merge a develop
Confirmá que no haya conflictos antes de hacer el merge

## 7. Eliminar la rama de funcionalidad

Una vez que el PR fue mergeado:
```bash
git branch -d feature/nombre-de-la-funcionalidad
git push origin --delete feature/nombre-de-la-funcionalidad
```