<?php

/**
 * Funciones de sanitización y validación de entradas.
 */

/**
 * Limpia texto básico eliminando espacios, saltos de línea y caracteres invisibles.
 */
function sanitizeInput(string $data): string
{
    $data = trim($data);
    $data = preg_replace('/[\x00-\x1F\x7F]/u', '', $data); // elimina caracteres de control
    $data = str_replace(["\r", "\n", "\t"], ' ', $data);  // normaliza saltos de línea
    return $data;
}

/**
 * Sanitiza direcciones de correo electrónico.
 */
function sanitizeEmail(string $email): string
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitiza valores enteros.
 */
function sanitizeInt($value): int
{
    return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Sanitiza valores decimales / flotantes.
 */
function sanitizeFloat($value): float
{
    return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

/**
 * Valida formato de email.
 */
function validateEmail(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valida la longitud de un texto.
 * Ejemplo: validateLength($nombre, 255, 3)
 */
function validateLength(string $data, int $max, int $min = 0): bool
{
    $len = mb_strlen($data);
    return $len >= $min && $len <= $max;
}

/**
 * Valida si una cadena contiene solo letras (y espacios opcionales).
 */
function validateLetters(string $data): bool
{
    return (bool) preg_match('/^[\p{L}\s]+$/u', $data);
}

/**
 * Valida si una cadena es alfanumérica (opcionalmente con espacios).
 */
function validateAlphanumeric(string $data, bool $allowSpaces = true): bool
{
    $pattern = $allowSpaces ? '/^[\p{L}\p{N}\s]+$/u' : '/^[\p{L}\p{N}]+$/u';
    return (bool) preg_match($pattern, $data);
}

/**
 * Valida contraseña segura.
 *  - Mínimo 8 caracteres
 *  - Al menos una mayúscula
 *  - Al menos una minúscula
 *  - Al menos un número
 *  - Opcionalmente: un carácter especial
 */
function validatePassword(string $password, bool $requireSpecial = false): bool
{
    $pattern = $requireSpecial
        ? '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        : '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/';
    return (bool) preg_match($pattern, $password);
}
