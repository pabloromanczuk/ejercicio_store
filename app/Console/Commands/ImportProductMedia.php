<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportProductMedia extends Command
{
    protected $signature = 'product:import-media {--path=assets/img/products : Directorio relativo a public/ con las imágenes}';

    protected $description = 'Registra en product_media las imágenes existentes en public/assets/img/products (idempotente y no destructivo).';

    public function handle(): int
    {
        $dir = public_path($this->option('path'));

        if (! is_dir($dir)) {
            $this->error("No existe el directorio: {$dir}");

            return self::FAILURE;
        }

        $archivos = collect(scandir($dir))
            ->filter(fn (string $f) => in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true))
            // El fallback no es la imagen de un producto: no debe registrarse.
            ->reject(fn (string $f) => $this->esFallback($f))
            ->values();

        if ($archivos->isEmpty()) {
            $this->warn('No se encontraron imágenes en el directorio.');

            return self::SUCCESS;
        }

        $productos = Product::all();

        $registrados = 0;
        $sinAsociar = [];

        foreach ($archivos as $archivo) {
            $path = trim($this->option('path'), '/').'/'.$archivo;

            // Idempotencia: si el path ya está registrado, no lo duplicamos.
            if (ProductMedia::where('path', $path)->exists()) {
                $this->line("Ya registrado: {$path}");
                continue;
            }

            $producto = $this->resolverProducto($archivo, $productos);

            if (! $producto) {
                $sinAsociar[] = $archivo;
                continue;
            }

            $producto->media()->create([
                'type' => ProductMedia::TYPE_IMAGE,
                'path' => $path,
                'sort_order' => $this->extraerNumeroOrden($archivo),
                'is_primary' => $producto->media()->doesntExist(),
            ]);

            $registrados++;
            $this->info("Registrada: {$path} -> {$producto->detalle}");
        }

        $this->newLine();
        $this->info("Imágenes registradas: {$registrados}.");

        if ($sinAsociar !== []) {
            $this->warn('No se pudieron asociar a un producto (no se registraron):');
            foreach ($sinAsociar as $archivo) {
                $this->line("  - {$archivo}");
            }
        } else {
            $this->info('Todas las imágenes se asociaron correctamente.');
        }

        return self::SUCCESS;
    }

    /**
     * Asocia un archivo (ej: "manzana_1.jpg") con un producto por su nombre.
     *
     * La convención es: nombre_(numero). El nombre base sin el sufijo "_N"
     * debe coincidir EXACTAMENTE (mismos caracteres) con el detalle del
     * producto, comparando ambos lados normalizados: minúsculas y sin
     * espacios al inicio/fin. Así tolera "Manzana", "manzanA" o "MANZANA"
     * pero NO acepta nombres distintos (ej: "manzanas" != "manzana").
     *
     * Si no hay coincidencia, se reporta el archivo en lugar de inventar
     * una asociación.
     *
     * @param  \Illuminate\Support\Collection<int, Product>  $productos
     */
    private function resolverProducto(string $archivo, $productos): ?Product
    {
        $nombre = $this->nombreBase($archivo);

        return $productos->first(function (Product $producto) use ($nombre) {
            return $this->normalizarNombre($producto->detalle) === $nombre;
        });
    }

    /**
     * Normaliza un nombre para compararlo: minúsculas y sin espacios
     * al inicio/fin. No altera el resto de los caracteres.
     */
    private function normalizarNombre(string $valor): string
    {
        return Str::lower(trim($valor));
    }

    /**
     * Indica si el archivo es el fallback global (fallback.png / fallback.jpg...).
     */
    private function esFallback(string $archivo): bool
    {
        return strtolower(pathinfo($archivo, PATHINFO_FILENAME)) === 'fallback';
    }

    /**
     * Extrae el nombre base de un archivo siguiendo la convención
     * "nombre_(numero)": manzana_1.jpg -> manzana.
     */
    private function nombreBase(string $archivo): string
    {
        $base = pathinfo($archivo, PATHINFO_FILENAME); // manzana_1
        return $this->normalizarNombre((string) preg_replace('/_\d+$/', '', $base)); // manzana
    }

    private function extraerNumeroOrden(string $archivo): int
    {
        if (preg_match('/_(\d+)$/', pathinfo($archivo, PATHINFO_FILENAME), $matches)) {
            return (int) $matches[1];
        }

        return 1;
    }
}
