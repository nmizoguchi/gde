<?php

namespace GDE;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuariosToken
 *
 * @ORM\Table(name="gde_usuarios_tokens", indexes={@ORM\Index(name="id_usuario", columns={"id_usuario"})})
 * @ORM\Entity
 */
class UsuariosToken extends Base {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_token", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id_token;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="token", type="string", length=255, nullable=false)
	 */
	protected $token;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="data_criacao", type="datetime", nullable=false)
	 */
	protected $data_criacao;

	/**
	 * @var \GDEGdeUsuarios
	 *
	 * @ORM\ManyToOne(targetEntity="Usuario")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
	 * })
	 */
	protected $id_usuario;


}
