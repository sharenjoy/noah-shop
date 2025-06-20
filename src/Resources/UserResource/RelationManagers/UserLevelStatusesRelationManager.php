<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use RalphJSmit\Filament\Activitylog\Tables\Actions\TimelineAction;
use Sharenjoy\NoahShop\Actions\Shop\RoleCan;
use Sharenjoy\NoahShop\Enums\UserLevelStatus as UserLevelStatusEnum;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Models\UserLevelStatus;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;

class UserLevelStatusesRelationManager extends RelationManager
{
    use CanViewShop;

    protected static string $relationship = 'userLevelStatuses';

    protected static ?string $icon = 'heroicon-o-chart-bar';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-cms::noah-cms.user_level_status');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-cms::noah-cms.user_level_status');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->userLevelStatuses->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(UserLevelStatus::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(UserLevelStatus $record): string => "({$record->id}) {$record->title}")
            ->heading(__('noah-cms::noah-cms.user_level_status'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(UserLevelStatus::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(UserLevelStatus::class, User::class))
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['title', 'description', 'slug'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\DetachAction::make(),
                TimelineAction::make(),
                Tables\Actions\EditAction::make()->visible(fn(): bool => RoleCan::run(role: 'super_admin')),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($action, $record) {
                        if ($record->status == UserLevelStatusEnum::On) {
                            Notification::make()
                                ->danger()
                                ->title('刪除失敗')
                                ->body('等級狀態為開啟狀態，無法刪除')
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->visible(fn(): bool => RoleCan::run(role: 'super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(function (Builder $query) {
                $sort = app(UserLevelStatus::class)->getSortColumn();
                foreach ($sort as $column => $direction) {
                    $query->orderBy($column, $direction);
                }
                return $query;
            });
    }
}
