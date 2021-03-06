<?php

namespace GDE;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Aluno
 *
 * @ORM\Table(name="gde_alunos", indexes={@ORM\Index(name="id_curso", columns={"id_curso"}), @ORM\Index(name="id_curso_pos", columns={"id_curso_pos"}), @ORM\Index(name="id_modalidade", columns={"id_modalidade"}), @ORM\Index(name="nome", columns={"nome"})})
 * @ORM\Entity
 */
class Aluno extends Base {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="ra", type="integer", options={"unsigned"=true}), nullable=false)
	 * @ORM\Id
	 */
	protected $ra;

	/**
	 * @var Usuario
	 *
	 * @ORM\OneToOne(targetEntity="Usuario", mappedBy="aluno")
	 */
	protected $usuario;

	/**
	 * @var Oferecimento
	 *
	 * @ORM\ManyToMany(targetEntity="Oferecimento", inversedBy="alunos")
	 * @ORM\JoinTable(name="gde_r_alunos_oferecimentos",
	 *      joinColumns={@ORM\JoinColumn(name="ra", referencedColumnName="ra")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="id_oferecimento", referencedColumnName="id_oferecimento")}
	 * )
	 */
	protected $oferecimentos;

	/**
	 * @var Oferecimento
	 *
	 * @ORM\ManyToMany(targetEntity="Oferecimento")
	 * @ORM\JoinTable(name="gde_r_alunos_trancadas",
	 *      joinColumns={@ORM\JoinColumn(name="ra", referencedColumnName="ra")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="id_oferecimento", referencedColumnName="id_oferecimento")}
	 * )
	 */
	protected $trancadas;

	/**
	 * @var Curso
	 *
	 * @ORM\ManyToOne(targetEntity="Curso")
	 * @ORM\JoinColumn(name="id_curso", referencedColumnName="id_curso")
	 */
	protected $curso;

	/**
	 * @var Curso
	 *
	 * @ORM\ManyToOne(targetEntity="Curso")
	 * @ORM\JoinColumn(name="id_curso_pos", referencedColumnName="id_curso")
	 */
	protected $curso_pos;

	/**
	 * @var Modalidade
	 *
	 * @ORM\ManyToOne(targetEntity="Modalidade")
	 * @ORM\JoinColumn(name="id_modalidade", referencedColumnName="id_modalidade")
	 */
	protected $modalidade;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nome", type="string", length=255, nullable=false)
	 */
	protected $nome;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nivel", type="string", length=1, nullable=true)
	 */
	protected $nivel;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nivel_pos", type="string", length=1, nullable=true)
	 */
	protected $nivel_pos;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="modalidade_pos", type="string", length=2, nullable=true)
	 */
	protected $modalidade_pos;

	/**
	 * Consultar
	 *
	 * Efetua uma consulta por Alunos
	 *
	 * @param $param
	 * @param null $ordem
	 * @param int $total
	 * @param string $limit
	 * @param string $start
	 * @return Aluno[]
	 */
	public static function Consultar($param, $ordem = null, &$total = 0, $limit = '-1', $start = '-1') {
		$qrs = $jns = array();
		if($ordem == null)
			$ordem = 'A.ra ASC';
		if(!empty($param['ra']))
			$qrs[] = "A.ra = :ra";
		if(!empty($param['nome'])) {
			$qrs[] = "A.nome LIKE :nome";
			$param['nome'] = '%'.$param['nome'].'%';
		}
		if(!empty($param['nivel']))
			$qrs[] = "A.nivel = :nivel";
		if(!empty($param['curso']))
			$qrs[] = "A.curso = :curso";
		if(!empty($param['modalidade']))
			$qrs[] = "A.modalidade = :modalidade";
		if(!empty($param['nivel_pos']))
			$qrs[] = "A.nivel_pos = :nivel_pos";
		if(!empty($param['curso_pos']))
			$qrs[] = "A.curso_pos = :curso_pos";
		if(!empty($param['modalidade_pos']))
			$qrs[] = "A.modalidade_pos = :modalidade_pos";
		if((!empty($param['gde'])) || (!empty($param['sexo'])) || (!empty($param['relacionamento'])) ||
			(!empty($param['cidade'])) || (!empty($param['estado'])) || (!empty($param['amigos']))) {

			$jns[] = " LEFT JOIN A.usuario AS U";
			if(!empty($param['gde']))
				$qrs[] = "U.usuario IS".(($param['gde'] == 't')?" NOT":null)." EMPTY";
			if(!empty($param['sexo']))
				$qrs[] = "U.sexo = :sexo";
			if(!empty($param['relacionamento']))
				$qrs[] = "U.estado_civil = :relacionamento";
			if(!empty($param['cidade'])) {
				$qrs[] = "U.cidade LIKE :cidade";
				$param['cidade'] = '%'.$param['cidade'].'%';
			}
			if(!empty($param['estado'])) {
				$qrs[] = "U.estado LIKE :estado";
				$param['estado'] = '%'.$param['estado'].'%';
			}
		}
		if(!empty($param['id_oferecimento'])) {
			$jns[] = "JOIN A.oferecimentos AS O";
			$qrs[] = "O.id_oferecimento = :id_oferecimento";
		} /*elseif((isset($param['oferecimentos'])) && (count($param['oferecimentos'][1] > 0))) {
			$mts = array();
			if($param['oferecimentos'][0]) { // AND
				$i = 0;
				foreach($param['oferecimentos'][1] as $oferecimento) {
					$jns[] = "JOIN A.oferecimentos AS O".$i."";
					$qrs[] = "O".$i.".periodo = :periodo AND O".$i.".sigla = ".$db->Quote($oferecimento[0]).(($oferecimento[1]!='*')?" AND O".$i.".turma = ".$db->Quote($oferecimento[1]):null);
					$i++;
				}
			} else { // OR
				$jns[] = " JOIN A.oferecimentos AS O";
				foreach($param['oferecimentos'][1] as $oferecimento)
					$mts[] = "(O.sigla = ".$db->Quote($oferecimento[0]).(($oferecimento[1]!='*')?" AND O.turma = ".$db->Quote($oferecimento[1]):null).")";
				$qrs[] = "O.periodo = :periodo AND (".implode(" OR ", $mts).")";
			}
		}*/ // ToDo
		unset($param['oferecimentos']);
		if(!empty($param['amigos'])) {
			$jns[] = " INNER JOIN U.amigos AS UA";
			$qrs[] = " UA.amigo = :id_usuario";
			unset($param['amigos']);
		}

		$joins = (count($jns) > 0) ? implode(" ", $jns) : null;
		$where = (count($qrs) > 0) ? "WHERE ".implode(" AND ", $qrs) : "";

		if($total !== null) {
			$dqlt = "SELECT COUNT(DISTINCT A.ra) FROM GDE\\Aluno AS A ".$joins." ".$where;
			$total = self::_EM()->createQuery($dqlt)->setParameters($param)->getSingleScalarResult();
		}

		if($ordem == 'A.ra ASC' || $ordem == 'A.ra DESC') {
			$extra_select =  ", (CASE WHEN A.ra<500000 THEN A.ra+1000000 ELSE A.ra END) AS HIDDEN ORD ";
			$ordem = ($ordem == 'A.ra ASC') ? "ORD ASC" : "ORD DESC";
		}

		$dql = "SELECT DISTINCT A".$extra_select." FROM GDE\\Aluno AS A ".$joins." ".$where." ORDER BY ".$ordem;
		$query = self::_EM()->createQuery($dql)->setParameters($param);
		if($limit > 0)
			$query->setMaxResults($limit);
		if($start > -1)
			$query->setFirstResult($start);
		return $query->getResult();
	}

	/**
	 * getOferecimentos
	 *
	 * Retorna a lista de Oferecimentos deste Aluno, opcionalmente filtrada por $periodo ou $niveis
	 *
	 * @param integer|null $periodo
	 * @param string[] $niveis
	 * @param bool $formatado
	 * @param bool $links
	 * @return string
	 */
	public function getOferecimentos($periodo = null, $niveis = array(), $formatado = false, $links = true) {
		if($niveis == 'G')
			$niveis = array('G', 'T');
		elseif(is_array($niveis) === false)
			$niveis = array($niveis);

		if(($periodo == null) && (count($niveis) == 0))
			$Oferecimentos = parent::getOferecimentos();
		else {
			$qb = self::_EM()->createQueryBuilder()
				->select('o')
				->from('GDE\\Oferecimento', 'o')
				->join('o.alunos', 'a')
				->where('a.ra = :ra')
				->setParameter('ra', $this->getRA(false));
			if($periodo != null) {
				$qb->join('o.periodo', 'p')
					->andWhere('p.id_periodo = :periodo')
					->setParameter('periodo', $periodo);
			}
			if(count($niveis) > 0) {
				$qb->join('o.disciplina', 'd')
					->andWhere('d.nivel IN (:niveis)')
					->setParameter('niveis', $niveis);
			}
			$Oferecimentos = $qb->getQuery()->getResult();
		}

		if($formatado === false)
			return $Oferecimentos;
		else {
			$lista = array();
			foreach($Oferecimentos as &$Oferecimento) {
				$lista[] = ($links)
					? "<a href=\"" . CONFIG_URL . "oferecimento/" .$Oferecimento->getID() . "\" title=\"" . $Oferecimento->getDisciplina(true)->getNome(true) . "\">" .
						$Oferecimento->getSigla(true) . $Oferecimento->getTurma() . "</a> (" . $Oferecimento->getDisciplina(true)->getCreditos(true) . ")"
					: $Oferecimento->getSigla(true) . $Oferecimento->getTurma(true) . " (" . $Oferecimento->getDisciplina(true)->getCreditos(true) . ")";
			}
			return (count($lista) > 0) ? implode(", ", $lista) : '-';
		}
	}

	/**
	 * getTrancadas
	 *
	 * Retorna a lista de Oferecimentos trancados deste Aluno, opcionalmente filtrada por $periodo ou $niveis
	 *
	 * @param integer|null $periodo
	 * @param string[] $niveis
	 * @param bool $formatado
	 * @return string
	 */
	public function getTrancadas($periodo = null, $niveis = array(), $formatado = false) {
		if($niveis == 'G')
			$niveis = array('G', 'T');
		elseif(is_array($niveis) === false)
			$niveis = array($niveis);

		if(($periodo == null) && (count($niveis) == 0))
			$Trancadas = parent::getTrancadas();
		else {
			$qb = self::_EM()->createQueryBuilder()
				->select('o')
				->from('GDE\\Oferecimento', 'o')
				->join('o.alunos_trancadas', 'a')
				->where('a.ra = :ra')
				->setParameter('ra', $this->getRA(false));
			if($periodo != null) {
				$qb->join('o.periodo', 'p')
					->andWhere('p.id_periodo = :periodo')
					->setParameter('periodo', $periodo);
			}
			if(count($niveis) > 0) {
				$qb->join('o.disciplina', 'd')
					->andWhere('d.nivel IN (:niveis)')
					->setParameter('niveis', $niveis);
			}
			$Trancadas = $qb->getQuery()->getResult();
		}

		if($formatado === false)
			return $Trancadas;
		else {
			$lista = array();
			foreach($Trancadas as $Trancada)
				$lista[] = "<a href=\"".CONFIG_URL."oferecimento/".$Trancada->getID()."\" title=\"".$Trancada->getDisciplina(true)->getNome(true)."\">".
					$Trancada->getSigla(true).$Trancada->getTurma(true)."</a> (".$Trancada->getDisciplina()->getCreditos(true).")";
			return (count($lista) > 0) ? implode(", ", $lista) : '-';
		}
	}

	/**
	 * Monta_Horario
	 *
	 * Monta um array de horario deste Aluno, opcionalmente filtrada por $periodo ou $niveis
	 *
	 * @param integer|null $periodo
	 * @param string[] $niveis
	 * @return array
	 */
	public function Monta_Horario($periodo = null, $niveis = array()) {
		if($niveis == 'G')
			$niveis = array('G', 'T');
		elseif(is_array($niveis) === false)
			$niveis = array($niveis);
		$Lista = array();
		foreach($this->getOferecimentos($periodo, $niveis) as $Oferecimento)
			foreach($Oferecimento->getDimensoes() as $Dimensao)
				$Lista[$Dimensao->getDia()][$Dimensao->getHorario()][] = array($Oferecimento, $Dimensao->getSala(true)->getNome(true));
		return $Lista;
	}

	/**
	 * Creditos_Atuais
	 *
	 * Retorna o numero de creditos deste Aluno, opcionalmente filtrada por $periodo ou $niveis
	 *
	 * @param null $periodo
	 * @param array $niveis
	 * @return mixed
	 */
	public function Creditos_Atuais($periodo = null, $niveis = array()) {
		if(is_object($periodo))
			$periodo = $periodo->getID();

		$dql = 'SELECT SUM(D.creditos) FROM GDE\\Aluno AS A '.
			'INNER JOIN A.oferecimentos AS O INNER JOIN O.disciplina AS D '.
			'WHERE A.ra = ?1 AND O.periodo = ?2';

		if(count($niveis) > 0)
			$dql .= ' AND D.nivel IN (?3)';

		$query = self::_EM()->createQuery($dql)
			->setParameter(1, $this->getRA(false))
			->setParameter(2, $periodo);

		if(count($niveis) > 0)
			$query->setParameter(3, $niveis);

		return $query->getSingleScalarResult();
	}

	/**
	 * Creditos_Trancados
	 *
	 * Retorna o numero de creditos trancados deste Aluno, opcionalmente filtrada por $periodo ou $niveis
	 *
	 * @param null $periodo
	 * @param array $niveis
	 * @return mixed
	 */
	public function Creditos_Trancados($periodo = null, $niveis = array()) {
		$creditos = 0;
		foreach($this->getTrancadas($periodo, $niveis) as $Oferecimento)
			$creditos += $Oferecimento->getDisciplina()->getCreditos();
		return $creditos;
	}

	/**
	 * Cursou
	 *
	 * Determina se este Aluno cursou $Disciplina
	 *
	 * @param Disciplina $Disciplina
	 * @return bool
	 */
	public function Cursou(Disciplina $Disciplina) {
		/*foreach($this->getOferecimentos(null, null) as $Oferecimento)
			if($Oferecimento->getSigla() == $Disciplina->getSigla())
				return true;
		return false;*/
		$dql = 'SELECT COUNT(O.id) FROM GDE\\Aluno AS A '.
			'INNER JOIN A.oferecimentos AS O '.
			'WHERE A.ra = ?1 AND O.sigla = ?2';

		$query = self::_EM()->createQuery($dql)
			->setParameter(1, $this->getRA(false))
			->setParameter(2, $Disciplina->getSigla());

		return ($query->getSingleScalarResult() > 0);
	}

	/**
	 * Trancou
	 *
	 * Determina se este Aluno trancou $Disciplina
	 *
	 * @param Disciplina $Disciplina
	 * @return bool
	 */
	public function Trancou(Disciplina $Disciplina) {
		/*foreach($this->getTrancadas(null) as $Oferecimento)
			if($Oferecimento->getSigla() == $Disciplina->getSigla())
				return true;
		return false;*/
		$dql = 'SELECT COUNT(O.id) FROM GDE\\Aluno AS A '.
			'INNER JOIN A.trancadas AS O '.
			'WHERE A.ra = ?1 AND O.sigla = ?2';

		$query = self::_EM()->createQuery($dql)
			->setParameter(1, $this->getRA(false))
			->setParameter(2, $Disciplina->getSigla());

		return ($query->getSingleScalarResult() > 0);
	}

}
