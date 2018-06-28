<?php

namespace InfyOm\Generator\Generators\Scaffold;

use Illuminate\Support\Str;
use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Utils\FileUtil;

class AuthGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $authContents;

    /** @var string */
    private $tokenGuardTemplate;

    /** @var string */
    private $sessionGuardTemplate;

    /** @var string */
    private $providersTemplate;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathAuth;
        $this->authContents = file_get_contents($this->path);

        $this->tokenGuardTemplate = get_template('scaffold.auth.guards.token', 'laravel-generator');
        $this->sessionGuardTemplate = get_template('scaffold.auth.guards.session', 'laravel-generator');
        $this->providersTemplate = get_template('scaffold.auth.providers.providers', 'laravel-generator');
       
        $this->providersTemplate = fill_template($this->commandData->dynamicVars, $this->providersTemplate);
        $this->tokenGuardTemplate = fill_template($this->commandData->dynamicVars, $this->tokenGuardTemplate);
        $this->sessionGuardTemplate = fill_template($this->commandData->dynamicVars, $this->sessionGuardTemplate);
    }

    public function generate()
    {
        $this->insertGuard();
        $this->insertProvider();

        file_put_contents($this->path, $this->authContents);
        $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' auth added.');
    }

    public function rollback()
    {
        $showMessage = false;
        if (Str::contains($this->authContents, $this->tokenGuardTemplate)) {
            $this->authContents = preg_replace("/'api-".$this->commandData->config->mCamel."'\s*=>\s*\[(\s*|[^\]])+\],\s*/", '', $this->authContents);
            file_put_contents($this->path, $this->authContents);
            $this->commandData->commandComment('scaffold token guard deleted');
            $showMessage = true;
        }

        if (Str::contains($this->authContents, $this->sessionGuardTemplate)) {
            $this->authContents = preg_replace("/'web-".$this->commandData->config->mCamel."'\s*=>\s*\[(\s*|[^\]])+\],\s*/", '', $this->authContents);
            file_put_contents($this->path, $this->authContents);
            $this->commandData->commandComment('scaffold session guard deleted');
            $showMessage = true;
        }

        if (Str::contains($this->authContents, $this->providersTemplate)) {
            $this->authContents = preg_replace("/'".$this->commandData->config->mCamelPlural."'\s*=>\s*\[(\s*|[^\]])*\],\s*/", '', $this->authContents);
            file_put_contents($this->path, $this->authContents);
            $this->commandData->commandComment('scaffold providers deleted');
            $showMessage = true;
        }

        if($showMessage)
        {
            $this->commandData->commandComment('scaffold authentication deleted');
        }
    }

    private function insertGuard()
    {
        $this->authContents = FileUtil::insert_after_regex("/'guards'\s*=>\s*\[/", "\n\t\t".$this->tokenGuardTemplate.",\n", $this->authContents);
        $this->authContents = FileUtil::insert_after_regex("/'guards'\s*=>\s*\[/", "\n\t\t".$this->sessionGuardTemplate.",\n", $this->authContents);        
    }

    private function insertProvider()
    {
        $this->authContents = FileUtil::insert_after_regex("/'providers'\s*=>\s*\[/", "\n\t\t".$this->providersTemplate.",\n", $this->authContents);        
    }


}
