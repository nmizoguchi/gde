<?php

namespace GDE;

use Doctrine\ORM\Mapping as ORM;

/**
 * MensagensDisciplina
 *
 * @ORM\Table(name="gde_mensagens_disciplinas", indexes={@ORM\Index(name="login", columns={"login"}), @ORM\Index(name="sigla", columns={"sigla"})})
 * @ORM\Entity
 */
class MensagensDisciplina extends Base {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_mensagem", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id_mensagem;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="sigla", type="string", length=5, nullable=false)
	 */
	protected $sigla;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="data", type="datetime", nullable=false)
	 */
	protected $data;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="texto", type="text", nullable=false)
	 */
	protected $texto;

	/**
	 * @var \GDEGdeUsuarios
	 *
	 * @ORM\ManyToOne(targetEntity="Usuario")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="login", referencedColumnName="login")
	 * })
	 */
	protected $login;


}
