<?php

namespace App\Controller;

use App\Entity\Codigo;
use App\Form\CodigoType;
use App\Repository\CodigoRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls as Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WXlsx;

/**
 * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
 * @Route("/codigo")
 */
class CodigoController extends AbstractController
{
    /**
     * @Route("/", name="codigo_index", methods={"GET"})
     */
    public function index(CodigoRepository $codigoRepository): Response
    {
        $codigos = $codigoRepository->findAll();

        $codigos = $this->filterCodes($codigos);

        return $this->render('codigo/index.html.twig', [
            'codigos' => $codigos,
        ]);
    }


    function generarCodigo($prefijo = 'COD')
    {
        $uniqueId = uniqid();
        $codigoUnico = md5($uniqueId);
        return $prefijo . '-' . $codigoUnico;
    }

    /**
     * @Route("/new", name="codigo_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $codigo = new Codigo();
        $form = $this->createForm(CodigoType::class, $codigo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cantidad = $form->get('cantidad')->getData();
            $codes = [];
            if ($cantidad > 0) {
                for ($i = 0; $i < $cantidad; $i++) {
                    $autoCode = new Codigo();
                    $autoCode->setFechaFin($codigo->getFechaFin())
                        ->setFechaInicio($codigo->getFechaInicio())
                        ->setTag($codigo->getTag())
                        ->setLibro($codigo->getLibro())
                        ->setActivo(false)
                        ->setCodebook($this->generarCodigo($codigo->getTag()));
                    $em->persist($autoCode);
                    $codes[]=$autoCode;
                }
                $em->flush();
                return $this->makeExcel($codes);
            }
//            return $this->redirectToRoute('codigo_index');
        }

        return $this->render('codigo/new.html.twig', [
            'codigo' => $codigo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function makeExcel(array $codigos): Response
    {
        $spreadsheet = new Spreadsheet();
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setCellValue('A1', 'Fecha Inicio');
        $hoja->setCellValue('B1', 'Fecha Fin');
        $hoja->setCellValue('C1', 'Tag');
        $hoja->setCellValue('D1', 'Libro');
        $hoja->setCellValue('E1', 'Activo');
        $hoja->setCellValue('F1', 'Codebook');

        $fila = 2;

        foreach ($codigos as $codigo) {
            $hoja->setCellValue('A' . $fila, $codigo->getFechaInicio()->format('Y-m-d'));
            $hoja->setCellValue('B' . $fila, $codigo->getFechaFin()->format('Y-m-d'));
            $hoja->setCellValue('C' . $fila, $codigo->getTag());
            $hoja->setCellValue('D' . $fila, $codigo->getLibro()->getNombre());
            $hoja->setCellValue('E' . $fila, $codigo->getActivo());
            $hoja->setCellValue('F' . $fila, $codigo->getCodebook());
            $fila++;
        }

        $writer = new WXlsx($spreadsheet);

        $response = new Response();
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="codigos.xlsx"');
        ob_start();
        $writer->save('php://output');
        $excelData = ob_get_clean();
        $response->setContent($excelData);
        return $response;
    }

    /**
     * @Route("/{id}", name="codigo_show", methods={"GET"})
     */
    public function show(Codigo $codigo): Response
    {
        return $this->render('codigo/show.html.twig', [
            'codigo' => $codigo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="codigo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Codigo $codigo): Response
    {
        $form = $this->createForm(CodigoType::class, $codigo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('codigo_index');
        }

        return $this->render('codigo/edit.html.twig', [
            'codigo' => $codigo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete", options={"expose"=true}, name="codigo_delete", methods={"POST"})
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $entityManager = $this->getDoctrine()->getManager();
            $codigo = $entityManager->getRepository(Codigo::class)->find($id);
            $entityManager->remove($codigo);
            $entityManager->flush();
            return new JsonResponse(['success' => 'Elemento eliminado correctamente']);
        } else {
            throw new Exception('¡Operación no permitida!');
        }
    }

    public function loadCodigo(UploadedFile $archivo, Codigo $codigo)
    {
        $pathFile = $archivo->getPathname();
        $extension = $archivo->guessClientExtension();
        $entityManager = $this->getDoctrine()->getManager();
        $rows = [];

        if ($extension === 'xlsx' || $extension === 'xls') {
            if ($extension === 'xlsx')
                $reader = new Xlsx();
            else
                $reader = new Xls();

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($archivo);
            $activeSheet = $spreadsheet->getActiveSheet();
//                $connection->executeUpdate($plattform->getTruncateTableSQL('codigo'));

            foreach ($activeSheet->getRowIterator() as $row) {
                $cells = $row->getCellIterator();
                $cells->setIterateOnlyExistingCells(true);
                $cellData = [];
                foreach ($cells as $cell) {
                    $cellData[0] = $cell->getValue();
                    if ($cellData[0] && $cellData[0] !== null) {
                        $codigoInsert = new Codigo();
                        $codigoInsert->setFechaInicio($codigo->getFechaInicio());
                        $codigoInsert->setFechaFin($codigo->getFechaFin());
                        $codigoInsert->setLibro($codigo->getLibro());
                        $codigoInsert->setCodebook($cellData[0]);
                        $entityManager->persist($codigoInsert);
                        $entityManager->flush();
                        array_push($rows, $cellData[0]);
                    }
                }
            }
        } else {
            return 'El archivo seleccionado no tiene el formato correcto';
        }
    }

    private function filterCodes(array $codes): array
    {
        $indexCodes = [];
        foreach ($codes as $i => $c) {
            $la = $c->getLibro()->getLibroActivados();
            foreach ($la as $l) {
                if ($c->getCodebook() === $l->getCodigoActivacion()) {
                    $indexCodes[] = $i;
                }
            }
        }
        foreach ($indexCodes as $i) {
            unset($codes[$i]);
        }

        array_values($codes);

        return $codes ? $codes : [];
    }
}
