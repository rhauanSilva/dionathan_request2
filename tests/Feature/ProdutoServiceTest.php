<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ProdutoServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProdutoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProdutoService();
    }

    // Teste 1: não cria produto com preço fora da regra de negócio
    public function test_nao_cria_produto_com_preco_invalido()
    {
        $this->expectException(InvalidArgumentException::class);

        try {
            $this->service->create(['nome' => 'Mouse', 'preco' => -10]);
        } finally {
            $this->assertDatabaseCount('produtos', 0);
        }
    }

    // Update 1: atualiza com dados válidos
    public function test_atualiza_produto_com_dados_validos()
    {
        $produto = Produto::create(['nome' => 'Teclado', 'preco' => 150.90]);

        $atualizado = $this->service->update($produto, [
            'nome'  => 'Teclado Gamer',
            'preco' => 199.90,
        ]);

        $this->assertEquals('Teclado Gamer', $atualizado->nome);
        $this->assertDatabaseHas('produtos', [
            'id'   => $produto->id,
            'nome' => 'Teclado Gamer',
        ]);
    }

    // Update 2: não atualiza se o preço for inválido
    public function test_nao_atualiza_produto_com_preco_invalido()
    {
        $produto = Produto::create(['nome' => 'Teclado', 'preco' => 150.90]);

        try {
            $this->service->update($produto, ['preco' => 0]);
            $this->fail('Deveria ter lançado InvalidArgumentException.');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('O preço deve ser maior que zero.', $e->getMessage());
        }

        $this->assertDatabaseHas('produtos', [
            'id'   => $produto->id,
            'nome' => 'Teclado',
        ]);
    }
}