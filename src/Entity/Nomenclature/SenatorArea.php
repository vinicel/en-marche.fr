<?php

namespace AppBundle\Entity\Nomenclature;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Nomenclature\SenatorAreaRepository")
 * @ORM\Table(name="nomenclature_senator_area", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="senator_area_code_unique", columns="code")
 * })
 * @UniqueEntity(fields="code", message="legislative_district_zone.area_code.unique")
 *
 * @Algolia\Index(autoIndex=false)
 */
class SenatorArea
{
    private const TYPE_DEPARTMENT = 'departement';
    private const TYPE_REGION = 'region';
    private const TYPE_DISTRICT = 'arrondissement';

    const ZONE_FRANCE = 'Département';
    const ZONE_DOM_TOM = 'Outre-Mer';
    const ZONE_FOREIGN = 'Étranger';
    const ZONE_ARRONDISSEMENT = 'Arrondissement';

    const TYPE_CHOICES = [
        'Département (Outre-Mer inclus)' => self::TYPE_DEPARTMENT,
        'Autre région du monde' => self::TYPE_REGION,
        'Arrondissement (Paris etc.)' => self::TYPE_DISTRICT,
    ];

    /**
     * @ORM\Column(type="smallint", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(length=6)
     *
     * @Assert\NotBlank
     */
    private $code;

    /**
     * @ORM\Column(length=20)
     *
     * @Assert\NotBlank
     * @Assert\Choice(
     *     callback="getTypeChoices",
     *     message="legislative_district_zone.area_type.invalid",
     *     strict=true
     * )
     */
    private $type = self::TYPE_DEPARTMENT;

    /**
     * @ORM\Column(length=100)
     *
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Count(min=1)
     */
    private $keywords;

    public static function createDepartmentZone(string $code, string $name, array $keywords = []): self
    {
        return self::create($code, self::TYPE_DEPARTMENT, $name, $keywords);
    }

    public static function createRegionZone(string $code, string $name, array $keywords = []): self
    {
        return self::create($code, self::TYPE_REGION, $name, $keywords);
    }

    public static function createDistrict(string $code, string $name, array $keywords = []): self
    {
        return self::create($code, self::TYPE_DISTRICT, $name, $keywords);
    }

    private static function create(string $code, string $type, string $name, array $keywords = []): self
    {
        $zone = new self();
        $zone->setCode($code);
        $zone->setType($type);
        $zone->setKeywords($keywords);
        $zone->setName($name);

        return $zone;
    }

    public static function getTypeChoices(): array
    {
        return array_values(self::TYPE_CHOICES);
    }

    public function __toString(): string
    {
        if (!$this->code) {
            return 'n/a';
        }

        return sprintf('%s - %s', $this->code, $this->name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setType(string $type): void
    {
        if (!\in_array($type = strtolower($type), $types = self::getTypeChoices())) {
            throw new \InvalidArgumentException(sprintf('Invalid area type "%s". It must be one of %s.', $type, implode(', ', $types)));
        }

        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    private static function isCorsica(string $code): bool
    {
        $char = substr($code, -1);

        return 'A' === $char || 'B' === $char;
    }

    public function getZoneNumber(): string
    {
        return ltrim($this->code, '0');
    }

    final public function getTypeLabel(): string
    {
        if (preg_match('/^[A-Z]/', strtoupper($this->code))) {
            return self::ZONE_FOREIGN;
        }

        if (self::isCorsica($this->code)) {
            return self::ZONE_FRANCE;
        }

        $code = (int) $this->code;

        if (5 === \strlen($code) && substr($code, 0, 2) <= 95) {
            return self::ZONE_ARRONDISSEMENT;
        }

        if ($code <= 95) {
            return self::ZONE_FRANCE;
        }

        if ($code >= 971 && $code <= 989) {
            return self::ZONE_DOM_TOM;
        }

        throw new \RuntimeException(sprintf('Unexpected code "%s" for zone "%s"', $code, $this->name));
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->addKeyword($name);
    }

    public function addKeyword(string $keyword): void
    {
        $keywords = $this->getKeywords();
        $keywords[] = $keyword;

        if ($keyword !== $lowercase = mb_strtolower($keyword, 'UTF-8')) {
            $keywords[] = $lowercase;
        }

        $this->setKeywords($keywords);
    }

    public function removeKeyword(string $keyword): void
    {
        $keywords = $this->getKeywords();

        if (false !== $key = array_search($keyword, $keywords, true)) {
            unset($keywords[$key]);
        }

        if (false !== $key = array_search(mb_strtolower($keyword, 'UTF-8'), $keywords, true)) {
            unset($keywords[$key]);
        }

        $this->setKeywords($keywords);
    }

    public function getKeywords(): array
    {
        if (empty($this->keywords)) {
            return [];
        }

        return explode("\n", $this->keywords);
    }

    public function setKeywords(array $keywords): void
    {
        $this->keywords = implode("\n", array_unique($keywords));
    }
}
