<?php

namespace GDE;

use Doctrine\ORM\Mapping as ORM;

/**
 * Equivalente
 *
 * @ORM\Table(name="gde_equivalentes")
 * @ORM\Entity
 */
class Equivalente extends Base {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_equivalente", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id_equivalente;

	/**
	 * @var Disciplina
	 *
	 * @ORM\ManyToOne(targetEntity="Disciplina", inversedBy="equivalentes")
	 * @ORM\JoinColumn(name="sigla", referencedColumnName="sigla")
	 */
	protected $disciplina;

	/**
	 * @var EquivalentesConjunto
	 *
	 * @ORM\OneToMany(targetEntity="EquivalentesConjunto", mappedBy="equivalente")
	 * @ORM\OrderBy({"sigla" = "ASC"})
	 */
	protected $conjuntos;

}
