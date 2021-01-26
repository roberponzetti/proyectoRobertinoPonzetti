<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Persona;
use App\Form\PersonaType;
use Symfony\Component\HttpFoundation\Request;

class PersonaController extends AbstractController
{

    /**
     * @Route("/inicio", name="inicio")
     */
    public function index(Request $request): Response
    {
        return $this->render('persona/index.html.twig');
    }

    /**
     * @Route("/registroPersona", name="registro")
     */
    public function registroPersona(Request $request): Response
    {
        $persona = new Persona();

        //Creo el formulario y que reciba la solicitud:
        $formulario = $this->createForm(PersonaType::class,$persona);
        $formulario -> handleRequest($request);

        // Para saber cuándo se hizo un click y si es válido el formulario.
        // Dentro del if cargo toda la información en la base de datos.

        if ($formulario->isSubmitted() && $formulario->isValid()){
                
            $entManager = $this->getDoctrine()->getManager();
            $entManager->persist($persona);
            $entManager->flush();
            return $this->render('persona/success.html.twig',
            ['persona' => $persona]
            );
        }

        return $this->render('persona/registroPersona.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }

    /**
     * @Route("/listaPersonas", name="listaPersonas")
     */

    public function listaPersonas(Request $request)
    {
        $manager=$this->getDoctrine()->getManager();
        $form = $this->createForm(PersonaType::class,new Persona());
        $form->handleRequest($request);
        
        $personas= $manager->getRepository(Persona::class)->findAll();
        
        return $this->render('persona/listaPersonas.html.twig',
                ['personas' => $personas]
            );
    }


    // Buscar la persona con el id recibido,cargarlo en el formulario, para luego crear una vista de el y pasarlo a modificarPersona.html. 
    // Si se presionó el botón submit, hace un UPDATE con la función flush(). 
    // Luego, llamo al controlador listarPersonas, donde se verán reflejado los nuevos cambios.

    /**
     * @Route("/modificarPersona/{id}", name="modificarPersona")
     */
    
    public function modificarPersona(Request $request, $id)
    {
        $manager=$this->getDoctrine()->getManager();
        
        $persona= $manager->getRepository(Persona::class)->find($id);
        
        $form = $this->createForm(PersonaType::class,$persona);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            
            $manager->flush();
            $this->addFlash(
                'success',
                'Usuario modificado correctamente.'
            );
            return $this->listaPersonas($request);
            
        }
        
        return $this->render('persona/modificarPersona.html.twig',
                ['formulario' => $form->createView()]
            );

    }

    // Para eliminar una persona, no es más que obtener la persona con el id, eliminarla (remove) y actualizar (flush).

    /**
     * @Route("/eliminarPersona/{id}", name="eliminarPersona")
     */
    
    public function eliminarPersona(Request $request, $id)
    {
        $manager=$this->getDoctrine()->getManager();
        
        $form = $this->createForm(PersonaType::class,new Persona());
        $form->handleRequest($request);
        
        $persona= $manager->getRepository(Persona::class)->find($id);
        
        $manager->remove($persona);
        $manager->flush();
        $this->addFlash(
            'success',
            'Usuario eliminado correctamente.'
        );
        return $this->listaPersonas($request);
        
    }
}
