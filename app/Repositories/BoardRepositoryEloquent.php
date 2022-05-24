<?php

namespace App\Repositories;

use App\Contracts\BoardTemplateRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\BoardRepository;
use App\Models\Board;
use App\Validators\BoardValidator;

/**
 * Class BoardRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BoardRepositoryEloquent extends BaseRepository implements BoardRepository
{

    /**
     * @var BoardTemplateRepository
     */
    private $boardTemplateRepo;

    public function __construct(Application $app, BoardTemplateRepository $boardTemplateRepository)
    {
        $this->boardTemplateRepo = $boardTemplateRepository;
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Board::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function fromTemplate(Request $request)
    {
        $template = $this->boardTemplateRepo->find($request->get('templateId', null));
        $shopId = $request->hasCookie('ace-shop-id') ? $request->cookie('ace-shop-id') : null;
        $content = $template->content;

        /**
         * @var $board Board
         */
        $board = $this->create([
            'shop_id'         => $shopId,
            'field_type_id'   => $template->field_type_id,
            'name'            => 'Copy Of - '.$template->name,
            'background_image' => $template->background_image,
            'panes'           => $content['panes']
        ]);

        foreach ($content['cards'] as $crntCard) {
            $board->cards()->create($crntCard);
        }
        return $board;
    }

}
