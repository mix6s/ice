<?php


namespace DomainBundle\DTO;


class GamesFilter
{
	private $fromDt;
	private $toDt;
	private $status;

	public static function fromData(array $data)
	{
		$filter = new self();
		if (!empty($data['from_dt'])) {
			$filter->setFromDt(new \DateTime($data['from_dt']));
		}
		if (!empty($data['to_dt'])) {
			$filter->setToDt(new \DateTime($data['to_dt']));
		}
		if ($data['status'] !== null && $data['status'] !== '') {
			$filter->setStatus((int)$data['status']);
		}
		return $filter;
	}

	public function setFromDt(\DateTime $fromDt)
	{
		$this->fromDt = $fromDt;
		return $this;
	}

	public function setToDt(\DateTime $toDt)
	{
		$this->toDt = $toDt;
		return $this;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getFromDt()
	{
		return $this->fromDt;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getToDt()
	{
		return $this->toDt;
	}

	/**
	 * @return string|null
	 */
	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus(int $status)
	{
		$this->status = $status;
		return $this;
	}

	public function hasStatus(): bool
	{
		return $this->status !== null;
	}

	public function hasFromDt(): bool
	{
		return $this->fromDt !== null;
	}

	public function hasToDt(): bool
	{
		return $this->toDt !== null;
	}
}