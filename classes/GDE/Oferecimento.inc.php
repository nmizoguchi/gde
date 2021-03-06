<?php

namespace GDE;

use Doctrine\ORM\Mapping as ORM;

/**
 * Oferecimento
 *
 * @ORM\Table(name="gde_oferecimentos", indexes={@ORM\Index(name="sigla", columns={"sigla"}), @ORM\Index(name="turma", columns={"turma"}), @ORM\Index(name="periodo", columns={"id_periodo"}), @ORM\Index(name="id_professor", columns={"id_professor"})})
 * @ORM\Entity
 */
class Oferecimento extends Base {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_oferecimento", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id_oferecimento;

	/**
	 * @var Disciplina
	 *
	 * @ORM\ManyToOne(targetEntity="Disciplina", inversedBy="oferecimentos")
	 * @ORM\JoinColumn(name="sigla", referencedColumnName="sigla")
	 */
	protected $disciplina;

	/**
	 * @var Periodo
	 *
	 * @ORM\ManyToOne(targetEntity="Periodo")
	 * @ORM\JoinColumn(name="id_periodo", referencedColumnName="id_periodo")
	 */
	protected $periodo;

	/**
	 * @var Professor
	 *
	 * @ORM\ManyToOne(targetEntity="Professor", inversedBy="oferecimentos")
	 * @ORM\JoinColumn(name="id_professor", referencedColumnName="id_professor")
	 */
	protected $professor;

	/**
	 * @var OferecimentoReserva
	 *
	 * @ORM\OneToMany(targetEntity="OferecimentoReserva", mappedBy="oferecimento")
	 */
	protected $reservas;

	/**
	 * @var Aluno
	 *
	 * @ORM\ManyToMany(targetEntity="Aluno", mappedBy="oferecimentos")
	 */
	protected $alunos;

	/**
	 * @var Aluno
	 *
	 * @ORM\ManyToMany(targetEntity="Aluno", mappedBy="trancadas")
	 */
	protected $alunos_trancadas;

	/**
	 * @var Dimensao
	 *
	 * @ORM\ManyToMany(targetEntity="Dimensao", inversedBy="oferecimentos")
	 * @ORM\JoinTable(name="gde_r_oferecimentos_dimensoes",
	 *      joinColumns={@ORM\JoinColumn(name="id_oferecimento", referencedColumnName="id_oferecimento")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="id_dimensao", referencedColumnName="id_dimensao")}
	 * )
	 */
	protected $dimensoes;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="turma", type="string", length=2, nullable=false)
	 */
	protected $turma;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="vagas", type="smallint", nullable=false)
	 */
	protected $vagas = '0';

	/**
	 * @var string
	 *
	 * @ORM\Column(name="fechada", type="boolean", nullable=false)
	 */
	protected $fechada = false;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="pagina", type="string", length=255, nullable=true)
	 */
	protected $pagina;

	/**
	 * Consultar
	 *
	 * Efetua uma consulta por Oferecimentos
	 *
	 * @param $param
	 * @param null $ordem
	 * @param null $total
	 * @param string $limit
	 * @param string $start
	 * @return Oferecimento[]
	 */
	public static function Consultar($param, $ordem = null, &$total = null, $limit = '-1', $start = '-1') {
		$qrs = $jns = array();
		if($ordem == null)
			$ordem = "O.id_oferecimento ASC";
		if(($ordem == "DI.sigla ASC") || ($ordem == "DI.sigla DESC"))
			$ordem = ($ordem != "DI.sigla DESC") ? "DI.sigla ASC, O.turma ASC" : "DI.sigla DESC, O.turma DESC";
		if(!empty($param['sigla']))
			if(strlen($param['sigla']) == 5) {
				$qrs[] = "DI.sigla = :sigla";
			} else {
				$qrs[] = "M.sigla LIKE :sigla";
				$param['sigla'] = '%'.$param['sigla'].'%';
			}
		if(!empty($param['periodo'])) {
			$qrs[] = "O.periodo = :periodo";
		}
		if((!empty($param['sigla'])) || (!empty($param['nome'])) || (!empty($param['creditos'])) || (!empty($param['instituto'])) || (!empty($param['nivel'])) || ($ordem == "DI.nome ASC") || ($ordem == "DI.nome DESC"))
			$jns[] = " JOIN O.disciplina AS DI";
		if(!empty($param['nome'])) {
			$qrs[] = "DI.nome LIKE :nome";
			$param['nome'] = '%'.$param['nome'].'%';
		}
		if(!empty($param['creditos']))
			$qrs[] = "DI.creditos = :creditos";
		if(!empty($param['instituto']))
			$qrs[] = "DI.instituto = :instituto";
		if(!empty($param['nivel']))
			$qrs[] = "DI.nivel = :nivel";
		if(!empty($param['turma']))
			$qrs[] = "O.turma = :turma";
		if(!empty($param['professor']))
			$qrs[] = "P.nome LIKE :professor";
		if(!empty($param['professor']) || ($ordem == "P.nome ASC") || ($ordem == "P.nome DESC"))
			$jns[] = " JOIN O.professor AS P";
		if((!empty($param['dia'])) || (!empty($param['horario'])) || (!empty($param['sala'])))
			$jns[] = " JOIN O.dimensoes AS D";
		if(!empty($param['dia']))
			$qrs[] = "D.dia = :dia";
		if(!empty($param['horario']))
			$qrs[] = "D.horario = :horario";
		if(!empty($param['sala']))
			$qrs[] = "D.sala LIKE :sala";
		$where = (count($qrs) > 0) ? implode(" AND ", $qrs) : "TRUE";
		$joins = (count($jns) > 0) ? implode(" ", $jns) : null;
		if($total !== null) {
			$dqlt = "SELECT COUNT(DISTINCT O.id_oferecimento) FROM GDE\\Oferecimento AS O ".$joins." WHERE ".$where;
			$total = self::_EM()->createQuery($dqlt)->setParameters($param)->getSingleScalarResult();
		}
		$dql = "SELECT DISTINCT O FROM GDE\\Oferecimento AS O ".$joins." WHERE ".$where." ORDER BY ".$ordem;
		$query = self::_EM()->createQuery($dql)->setParameters($param);
		if($limit > 0)
			$query->setMaxResults($limit);
		if($start > -1)
			$query->setFirstResult($start);
		return $query->getResult();
	}

	/**
	 * getSigla
	 *
	 * Retorna a sigla da Disciplina deste Oferecimento
	 *
	 * @param bool $html
	 * @return null
	 */
	public function getSigla($html = false) {
		if($this->getDisciplina(false) === null)
			return null;
		return $this->getDisciplina()->getSigla($html);
	}

	/**
	 * getReservas
	 *
	 * Retorna a lista de Reservas deste Oferecimento, opcionalmente formatadas (HTML)
	 *
	 * @param bool $formatado
	 * @return OferecimentoReserva[]|string
	 */
	public function getReservas($formatado = false) {
		$Reservas = parent::getReservas();
		if($formatado === false)
			return $Reservas;
		else {
			if($Reservas->count() == 0)
				return 'N&atilde;o Dispon&iacute;vel';
			if($Reservas->first()->getCurso(false) === null)
				return 'Sem Reservas';
			else {
				$lista = array();
				foreach($Reservas as $Reserva)
					$lista[] = $Reserva->getCurso(true)->getNome(true)." (".$Reserva->getCurso(true)->getNumero(true).")".(($Reserva->getCatalogo(false) != null) ? " / ".$Reserva->getCatalogo(true) : null);
				return implode("<br />", $lista);
			}
		}
	}

	/**
	 * Monta_Horario
	 *
	 * Organiza as Dimensoes deste Oferecimento
	 *
	 * @return array
	 */
	public function Monta_Horario() {
		$Lista = array();
		foreach($this->getDimensoes() as $Dimensao)
			$Lista[$Dimensao->getDia()][$Dimensao->getHorario()] = $Dimensao->getSala()->getNome(true);
		return $Lista;
	}

	/**
	 * Lista_Horario
	 *
	 * Retorna uma lista das Dimensoes deste Oferecimento
	 *
	 * @param bool $cru
	 * @return array
	 */
	public function Lista_Horarios($cru = false) {
		$Lista = array();
		foreach($this->getDimensoes() as $Dimensao)
			$Lista[] = ($cru) ? $Dimensao->getDia().sprintf("%02d", $Dimensao->getHorario()) : array($Dimensao->getDia(), $Dimensao->getHorario(), $Dimensao->getSala()->getNome(true));
		return $Lista;
	}

	/**
	 * Formata_Horario
	 *
	 * Retorna um horario formatado para este Oferecimento
	 *
	 * @param $Horario
	 * @param $dia
	 * @param $horario
	 * @return string
	 */
	public static function Formata_Horario($Horario, $dia, $horario) {
		return (isset($Horario[$dia][$horario])) ? (($Horario[$dia][$horario] != '????') ? "<a href=\"".CONFIG_URL."sala/".$Horario[$dia][$horario]."\">".$Horario[$dia][$horario]."</a>" : $Horario[$dia][$horario]) : "-";
	}

	/**
	 * Matriculados
	 *
	 * Retorna o numero de Alunos matriculados neste Oferecimento
	 *
	 * @return integer
	 */
	public function Matriculados() {
		return $this->getAlunos()->count();
	}

	/**
	 * Desistencias
	 *
	 * Retorna o numero de Alunos que trancaram este Oferecimento
	 *
	 * @return integer
	 */
	public function Desistencias() {
		return $this->getAlunos_Trancadas()->count();
	}

}
