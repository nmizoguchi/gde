<?php

namespace GDE;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostagensVoto
 *
 * @ORM\Table(name="gde_postagens_votos")
 * @ORM\Entity
 */
class PostagensVoto extends Base {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_usuario", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	protected $id_usuario;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_postagem", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	protected $id_postagem;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tipo_voto", type="string", length=1, nullable=false)
	 */
	protected $tipo_voto;


}
