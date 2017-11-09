<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;


trait ManageTagsTrait
{
    private $tags = [];

    public function getTags()
    {
        return $this->tags;
    }

    public function alterTags($tags)
    {
        $inserted = [];
        $deleted = array_flip($this->getTags());
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if (empty($tag)) {
                    continue;
                }
                $this->addTag($tag);
                unset($deleted[$tag]);
            }
        }

        $deleted = array_values(array_flip($deleted));
        //check that tags are unused into members
        $this->removeTags($deleted);

        return [
            'inserted' => $inserted,
            'deleted' => $deleted,
        ];
    }

    private function hasTag($tag)
    {
        return in_array($tag, $this->tags);
    }

    private function addTag($tag)
    {
        if ($this->hasTag($tag)) {
            return;
        }
        $this->tags[] = $tag;
    }

    private function removeTags($tags)
    {
        foreach ($this->tags as $key => $tag) {
            if (in_array($tag, $tags)) {
                unset($this->tags[$key]);
            }
        }

        $this->tags = array_values($this->tags);
    }

}
