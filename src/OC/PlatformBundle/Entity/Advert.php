<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Advert
 *
 * @ORM\Table(name="oc_advert")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Entity\AdvertRepository")
 */
class Advert
{

    /**
	* @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedByd="advert")
	*/
	private $applications; // Note le "s", une annonce st liée a plusieurs candidatures
	
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
	
	/**
	 * @ORM\Column(name="author", type="string", length=255)
	 */
	protected $author;
	 
	 /**
	  * @ORM\Column(name="content", type="text")
	  */
	protected $content;
	
	/**
	 * @ORM\Column(name="published", type="boolean")
	 */
	private $published = true;

	/**
	 * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist"})
	 */
	private $image;
	
	/**
	 * @ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"});
	 */
	private $categories;
	
	public function __construct()
	{
		$this->applications = new ArrayCollection();
	   //Par default, la date de l'annonce est la date du jour
	   $this->date = new \Datetime();
	   $this->categories = new ArrayCollection();
	}
	
	public function addApplication(Application $application)
	{
	  $this->applications[] = $application;
	  // On lie l'annonce à la candidature
	  $application->setAdvert($this);
	  return $this;
	}
	
	public function removeApplication(Application $application)
	{
	  $this->applications->removeElement($application);
	}
	
	public function getApplications()
	{
	  return $this->applications;
	}


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set image
     *
     * @param \OC\PlatformBundle\Entity\Image $image
     * @return Advert
     */
    public function setImage(\OC\PlatformBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \OC\PlatformBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
	
	// Notez le singulier, on ajoute une seule categorie à la fois
	public function addCategory(Category $category)
	{
	  // Ici, on utilise l'ArrayCollection vraiment comme un tableau
	  $this->categories[] = $category;
	  
	  return $this;
	}
	
	public function removeCategory(Category $category)
	{
	  // Ici on utilise une methode de l'ArrayCollection, pour supprimer la categorie en argument
	  $this->categories->removeElement($category);
	}
	

}
