<?php
/**
 *
 * User: xiaowei<13177839316@163.com>
 * Date: 2020/3/20
 * Time: 15:07
 */

namespace App\Services;


use App\Exception\WrongRequestException;
use App\Model\Blog\Article;
use App\Model\Blog\Tag;

class TagService
{

    /**
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function tagList()
    {
        return Tag::query()->where('status',Tag::NORMAL_STATUS)
                            ->orderByDesc('level')
                            ->orderByDesc('id')
                            ->get();
    }

    /**
     * @param $params
     *
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public function pagination($params) {

        return Tag::query()->orderByDesc('id')->paginate(10);
    }

    /**
     * @param $name
     *
     * @return int
     */
    protected function hasSameName($name) {
        return Tag::query()->where('name',$name)->count();
    }

    /**
     * @param $params
     *
     * @return bool
     */
    public function save($params) {
        if (isset($params['id'])) {
            $tag = Tag::query()->where('id',$params['id'])->first();
            if ($tag->name != $params['name'] && $this->hasSameName($params['name']) ) {
                throw new WrongRequestException("存在相同名称的标签!");
            }
        } else {
            if ( $this->hasSameName($params['name']) ){
                throw new WrongRequestException("存在相同名称的标签!");
            }
            $tag = new Tag();
        }

        $tag->name   = $params['name'];
        $tag->status = $params['status'];
        $tag->level  = $params['level'];
        $tag->type   = $params['type'];

        return $tag->save();
    }

    /**
     * @param $id
     *
     * @return int|mixed
     */
    public function delete($id) {
        $count = Article::query()->where('tag_id',$id)->count();
        if ( $count == 0 ) {
            return Tag::query()->where('id',$id)->delete();
        } else {
            throw new WrongRequestException("该标签下还有内容,无法删除!");
        }
    }


    /**
     * @param $id
     *
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function row($id) {
        return Tag::query()->where('id',$id)->first();
    }
}
