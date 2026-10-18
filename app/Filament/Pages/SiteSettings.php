<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.site-settings';

    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Contact & Social')
                    ->schema([
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp Number')
                            ->placeholder('+6282228764381')
                            ->required(),
                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->required(),
                        TextInput::make('catalog_url')
                            ->label('Catalog PDF File URL')
                            ->placeholder('/catalog.pdf'),
                        TextInput::make('video_url')
                            ->label('YouTube Video URL')
                            ->placeholder('https://youtu.be/8AHPXm9Y6mI'),
                        TextInput::make('youtube_id')
                            ->label('YouTube Video ID')
                            ->placeholder('8AHPXm9Y6mI'),
                    ])->columns(2),

                Section::make('Location & Map')
                    ->schema([
                        TextInput::make('address')
                            ->label('Factory / Office Address')
                            ->columnSpanFull(),
                        TextInput::make('map_lat')
                            ->label('Map Latitude'),
                        TextInput::make('map_lng')
                            ->label('Map Longitude'),
                    ])->columns(2),

                Section::make('Footer & Branding')
                    ->schema([
                        Textarea::make('hero_heading')
                            ->label('Hero Heading')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('hero_subheading')
                            ->label('Hero Subheading')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('footer_tagline')
                            ->label('Footer Tagline')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('copyright_text')
                            ->label('Copyright Text')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }
}
