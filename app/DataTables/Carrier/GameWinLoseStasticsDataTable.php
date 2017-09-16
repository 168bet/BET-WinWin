<?php

namespace App\DataTables\Carrier;

use App\Models\Log\GameWinLoseStastics;
use Form;
use Yajra\Datatables\Services\DataTable;

class GameWinLoseStasticsDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('action', 'game_win_lose_stastics.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $gameWinLoseStastics = GameWinLoseStastics::with('gamePlat');

        return $this->applyScopes($gameWinLoseStastics);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->ajax([
                'data' => \Config::get('datatables.ajax.data')
            ])
            ->parameters([
                'paging' => true,
                'searching' => false,
                'ordering' => false,
                'info' => true,
                'dom' => 'Bfrtipl',
                'scrollX' => false,
                'buttons' => [
                ],
                'language' => \Config::get('datatables.language'),
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    private function getColumns()
    {
        return [
            '游戏平台' => ['name' => 'game_plat_id', 'data' => 'game_plat.game_plat_name'],
            '投注人数' => ['name' => 'bet_player_count', 'data' => 'bet_player_count'],
            '投注次数' => ['name' => 'bet_count', 'data' => 'bet_count'],
            '投注额' => ['name' => 'bet_amount', 'data' => 'bet_amount'],
            '公司总输赢' => ['name' => 'win_lose_amount', 'data' => 'win_lose_amount'],
            '洗码' => ['name' => 'rebate_financial_flow_amount', 'data' => 'rebate_financial_flow_amount'],
            '人均投注额' => ['name' => 'average_bet_amount', 'data' => 'average_bet_amount'],
            '人均投注次数' => ['name' => 'average_bet_count', 'data' => 'average_bet_count'],
            '更新时间' => ['name' => 'updated_at', 'data' => 'updated_at']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'gameWinLoseStastics';
    }
}
