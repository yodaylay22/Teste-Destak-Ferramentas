<?php

namespace App\Filament\Resources;

// Recursos e Páginas do Filament
use App\Filament\Resources\ProductResource\Pages;

// Models
use App\Models\MlCategory;
use App\Models\Product;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Leandrocfe\FilamentPtbrFormFields\Money;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

// Filament Notifications
use Filament\Notifications\Notification;

// Filament Resource
use Filament\Resources\Resource;

// Facades do Laravel
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 0;
    
    protected static ?string $pluralLabel = 'produtos';
    protected static ?string $modelLabel = 'produto';

    public static function getNavigationBadge(): ?string{
        return static::getModel()::where('user_id', auth()->user()->id)->count();
    }

    public static function getNavigationBadgeColor(): ?string{
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Produto')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome')
                        ->required(),
                    Forms\Components\Select::make('category')
                        ->label('Categoria')
                        ->hint('Retornando sucesso com a categoria "Cubo Mágico"')
                        ->options(MlCategory::all()->pluck('name', 'id_ml')->toArray())
                        ->required()
                        ->searchable()
                        ->loadingMessage('Carregando categorias...')
                        ->noSearchResultsMessage('Nenhuma categoria encontrada')
                        ->placeholder('Selecione uma categoria'),
                    Money::make('price')
                        ->label('Preço')
                        ->intFormat()
                        ->required(),
                    \LaraZeus\Quantity\Components\Quantity::make('stock_quantity')
                        ->label('Estoque')
                        ->default(0)
                        ->steps(1)
                        ->required(),
                ])->columns(2),
                Section::make()
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Descrição')
                        ->rows(3)
                        ->autosize()
                        ->required(),
                ])->columns(1),
                Section::make()
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Imagem')
                        ->required()
                        ->image()
                        ->openable()
                        ->downloadable(),
                ])->columns(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem'),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nome')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('mlCategory.name')
                    ->label('Categoria')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL', divideBy: 100)
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Estoque')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('ml_id')
                    ->label('ID Mercado Livre')
                    ->placeholder('Não enviado para o Mercado Livre')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('EnviarProduto')
                    ->label('Enviar Produto')
                    ->icon('heroicon-o-arrow-up-right')
                    ->color('info')
                    ->hidden(fn ($record) => $record->ml_id)
                    ->action(function ($record) {
    
                        $dadosProduto = [
                            "title"              => $record->name,
                            "category_id"        => $record->category,
                            "price"              => number_format($record->price/100, 2, '.', ''),
                            "currency_id"        => 'BRL',
                            "available_quantity" => $record->stock_quantity,
                            "buying_mode"        => "buy_it_now",
                            "listing_type_id"    => "gold_special",
                            "condition"          => "new",
                            "description"        => [
                                "plain_text" => $record->description
                            ],
                            "pictures" => [
                                [
                                    "source" => config('app.url').Storage::url($record->image)
                                ]
                            ],
                            "attributes" => [
                                [
                                    "id" => "BRAND",
                                    "value_name" => "Marca"
                                ],
                                [
                                    "id" => "MODEL",
                                    "value_name" => "Modelo"
                                ],
                                [
                                    "id" => "RECOMMENDED_AMBIENTS",
                                    "value_name" => "Ambiente recomendado"
                                ]
                            ]
                        ];
            
                        $response = Http::withToken(auth()->user()->mercadolibre_token)->post('https://api.mercadolibre.com/items', $dadosProduto);
                        $data = $response->json();

                        if($response->successful()){    
                            $record->update(['ml_id' => $data['id']]);
                            Notification::make()->title('Produto enviado com sucesso!')->success()->duration(11000)->send();
                        } else {
                            if (isset($data['cause']) && is_array($data['cause'])) {
                                foreach ($data['cause'] as $error) {
                                    Notification::make()->title('Erro no envio do produto')->body($error['message'])->danger()->duration(11000)->send();
                                }
                            }

                            if($data['message'] AND $data['error']) Notification::make()->title($data['message'])->body($data['error'])->danger()->duration(11000)->send();
                        }
                    })->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
