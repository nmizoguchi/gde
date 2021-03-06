<?php

namespace GDE;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evento
 *
 * @ORM\Table(name="gde_eventos", indexes={@ORM\Index(name="data_inicio", columns={"data_inicio", "data_fim"}), @ORM\Index(name="data_aviso", columns={"data_aviso"}), @ORM\Index(name="id_usuario", columns={"id_usuario"})})
 * @ORM\Entity
 */
class Evento extends Base {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_evento", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id_evento;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_usuario", type="integer", options={"unsigned"=true}), nullable=true)
	 */
	protected $id_usuario;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_oferecimento", type="integer", options={"unsigned"=true}), nullable=true)
	 */
	protected $id_oferecimento;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nome", type="string", length=255, nullable=false)
	 */
	protected $nome;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tipo", type="string", length=1, nullable=false)
	 */
	protected $tipo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="descricao", type="text", nullable=true)
	 */
	protected $descricao;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="local", type="text", nullable=true)
	 */
	protected $local;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="data_inicio", type="datetime", nullable=false)
	 */
	protected $data_inicio;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="data_fim", type="datetime", nullable=true)
	 */
	protected $data_fim;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="dia_todo", type="boolean", nullable=false)
	 */
	protected $dia_todo = false;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tipo_aviso", type="string", length=1, nullable=true)
	 */
	protected $tipo_aviso;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="data_aviso", type="datetime", nullable=true)
	 */
	protected $data_aviso;


}
