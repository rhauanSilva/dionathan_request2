<?php

namespace App\Services;

use App\Models\Produto;
use InvalidArgumentException;

class ProdutoService
{
    public function create(array $data): Produto
    {
        $this->validarPreco($data['preco'] ?? null);

        return Produto::create($data);
    }

    public function update(Produto $produto, array $data): Produto
    {
        if (array_key_exists('preco', $data)) {
            $this->validarPreco($data['preco']);
        }

        $produto->update($data);

        return $produto;
    }

    private function validarPreco($preco): void
    {
        if (!is_numeric($preco) || $preco <= 0) {
            throw new InvalidArgumentException('O preço deve ser maior que zero.');
        }
    }
}