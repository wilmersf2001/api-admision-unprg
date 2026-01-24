<?php

// app/Http/Traits/HandlesValidation.php
namespace App\Http\Traits;

use Illuminate\Validation\ValidationException;

trait HandlesValidation
{
    /**
     * Throw validation exception for invalid credentials
     */
    protected function throwInvalidCredentials(): void
    {
        throw ValidationException::withMessages([
            'credentials' => ['Las credenciales proporcionadas son incorrectas.']
        ]);
    }

    /**
     * Throw validation exception for specific field
     */
    protected function throwFieldValidationError(string $field, string $message): void
    {
        throw ValidationException::withMessages([
            $field => [$message]
        ]);
    }

    /**
     * Throw validation exception for multiple fields
     */
    protected function throwValidationErrors(array $errors): void
    {
        throw ValidationException::withMessages($errors);
    }

    /**
     * Common validation errors
     * @throws ValidationException
     */
    protected function throwUserNotFound(): void
    {
        $this->throwInvalidCredentials(); // Por seguridad, no revelamos si el usuario existe
    }

    /**
     * @throws ValidationException
     */
    protected function throwInvalidPassword(): void
    {
        $this->throwInvalidCredentials(); // Por seguridad, mismo mensaje
    }

    protected function throwAccountDisabled(): void
    {
        throw ValidationException::withMessages([
            'account' => ['Tu cuenta se encuentra desactivada.']
        ]);
    }

    protected function throwEmailNotVerified(): void
    {
        throw ValidationException::withMessages([
            'email' => ['Debes verificar tu correo electrónico antes de continuar.']
        ]);
    }
}
