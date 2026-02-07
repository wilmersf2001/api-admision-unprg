<?php

namespace App\Http\Services;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Subir un archivo al storage de Laravel
     *
     * @param UploadedFile $file El archivo a subir
     * @param bool $isPublic Si el archivo es público o privado
     * @param string|null $directory Directorio dentro del disco (opcional)
     * @return File El modelo del archivo creado
     */
    public function upload(Model $entity, UploadedFile $file, bool $isPublic, string $type, string $typeEntitie, ?string $directory = null): File
    {
        // Determinar el disco según si es público o privado(local)
        $disk = $isPublic ? 'public' : 'local';

        // Generar un nombre único para el archivo
        $extension = $file->getClientOriginalExtension();
        $name = Str::uuid() . '.' . $extension;

        // Construir la ruta completa
        $path = $directory ? $directory . '/' . $name : $name;

        // Guardar el archivo en el disco
        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

        // Obtener dimensiones si es imagen
        $dimensions = $this->getImageDimensions($file);

        // Crear el registro en la base de datos
        return File::create([
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
            'type' => $type,
            'type_entitie' => $typeEntitie,
            'path' => $path,
            'disk' => $disk,
            'is_public' => $isPublic,
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
            'status' => true,
        ]);
    }

    /**
     * Obtener la URL de un archivo
     *
     * @param File $file El modelo del archivo
     * @return string La URL del archivo
     */
    public function getUrl(File $file): string
    {
        if ($file->is_public) {
            return Storage::disk($file->disk)->url($file->path);
        }

        // Para archivos PRIVADOS, generar URL temporal (válida por 60 minutos)
        //return Storage::disk($file->disk)->temporaryUrl($file->path, now()->addMinutes(60));
        // Para archivos privados, generar URL temporal (válida por 3 segundos now()->addSeconds(3))
        return Storage::disk($file->disk)->temporaryUrl($file->path, now()->addSeconds(3));
    }

    /**
     * Eliminar un archivo del storage y de la base de datos
     *
     * @param File $file El modelo del archivo a eliminar
     * @param bool $forceDelete Si se debe eliminar permanentemente
     * @return bool
     */
    public function delete(File $file, bool $forceDelete = false): bool
    {
        // Eliminar el archivo físico del storage
        if (Storage::disk($file->disk)->exists($file->path)) {
            Storage::disk($file->disk)->delete($file->path);
        }

        // Eliminar el registro de la base de datos
        if ($forceDelete) {
            return $file->forceDelete();
        }

        return $file->delete();
    }

    /**
     * Actualizar los datos de un archivo
     *
     * @param File $file El modelo del archivo
     * @param array $data Los datos a actualizar
     * @return File
     */
    public function update(File $file, array $data): File
    {
        $file->update($data);
        return $file->fresh();
    }

    /**
     * Cambiar la visibilidad de un archivo (público/privado)
     *
     * @param File $file El modelo del archivo
     * @param bool $isPublic Nueva visibilidad
     * @return File
     */
    public function changeVisibility(File $file, bool $isPublic): File
    {
        $newDisk = $isPublic ? 'public' : 'local';

        // Si el disco cambia, mover el archivo
        if ($file->disk !== $newDisk) {
            // Leer el contenido del archivo actual
            $content = Storage::disk($file->disk)->get($file->path);

            // Guardar en el nuevo disco
            Storage::disk($newDisk)->put($file->path, $content);

            // Eliminar del disco antiguo
            Storage::disk($file->disk)->delete($file->path);

            // Actualizar el modelo
            $file->update([
                'disk' => $newDisk,
                'is_public' => $isPublic,
            ]);
        }

        return $file->fresh();
    }

    /**
     * Obtener las dimensiones de una imagen
     *
     * @param UploadedFile $file
     * @return array
     */
    private function getImageDimensions(UploadedFile $file): array
    {
        $dimensions = [];

        // Verificar si es una imagen
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                $dimensions['width'] = $imageInfo[0];
                $dimensions['height'] = $imageInfo[1];
            }
        }

        return $dimensions;
    }

    /**
     * Obtener archivos por tipo MIME
     *
     * @param string $mimeType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByMimeType(string $mimeType)
    {
        return File::where('mime_type', 'like', $mimeType . '%')
            ->where('status', true)
            ->get();
    }

    /**
     * Obtener archivos públicos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublicFiles()
    {
        return File::where('is_public', true)
            ->where('status', true)
            ->get();
    }

    /**
     * Obtener archivos privados
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrivateFiles()
    {
        return File::where('is_public', false)
            ->where('status', true)
            ->get();
    }
}
