<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProfessorRepository;

use Dompdf\Dompdf;

class ProfessorController extends AbstractController
{
    private ProfessorRepository $repository;

    public function __construct()
    {
        $this->repository = new ProfessorRepository();
    }

    public function listar(): void
    {
        $rep = new ProfessorRepository();
        $professores = $rep->buscarTodos();

        $this->render("professor/listar", [
            'professores' => $professores,
        ]);
    }

    public function cadastrar(): void
    {
        echo "Pagina de cadastrar";
    }

    public function excluir(): void
    {
        $id = $_GET['id'];
        $rep = new ProfessorRepository();
        $rep->excluir($id);
        $this->redirect("/professores/listar");
    }

    public function editar(): void
    {
        
    }

    private function redirecionar(iterable $professores){
        $resultado = '';
        foreach ($professores as $professor) {
        $resultado .= "
            <tr>
                <td>{$professor->id}</td>
                <td>{$professor->nome}</td>
                <td>{$professor->endereco}</td>
                <td>{$professor->formacao}</td>
                <td>{$professor->status}</td>
                <td>{$professor->cpf}</td>
            </tr>";
            }
            return $resultado;
    }

    public function relatorio(): void
    {
        $hoje = date('d/m/Y');
        $professor = $this->repository->buscarTodos();
        $design = "
        <h1>Relatorio de Professores</h1>
        <hr>
        <em>Gerando em {$hoje}</em>
        <hr>
        <table border='1' width='100%' style='margin-top: 30px;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Formação</th>
                    <th>Status</th>
                    <th>CPF</th>
                </tr>
            </thead>
            <tbody>
            ".$this->redirecionar($professor)."
            </tbody>
        </table>
        ";

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait'); 
        $dompdf->loadHtml(($design)); 
        $dompdf->render();
        $dompdf->stream('Relatorio-Professores.pdf', ['Attachment' => 0]); 
    }
}