<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class UpdateUserPasswordAction
{
    public static function make()
    {
        return Action::make('updateOrderStatusAction')
            ->label(__('noah-cms::noah-cms.update_password'))
            ->modalHeading(__('noah-cms::noah-cms.update_password'))
            ->color('primary')
            ->icon('heroicon-o-pencil-square')
            ->form([
                Section::make('')
                    ->extraAttributes(['style' => 'background-color: #f8f8f8'])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('password')
                                    ->label(__('noah-cms::noah-cms.password'))
                                    ->password()
                                    ->required()
                                    ->placeholder('********')
                                    ->rules(['min:8']),
                                TextInput::make('password_confirmation')
                                    ->label(__('noah-cms::noah-cms.password_confirmation'))
                                    ->password()
                                    ->placeholder('********')
                                    ->required()
                                    ->same('password'),
                            ]),
                    ]),
            ])
            ->action(function (array $data, $record) {
                $record->password = bcrypt($data['password']);
                $record->save();

                Notification::make()
                    ->title(__('noah-cms::noah-cms.password_updated'))
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
}
