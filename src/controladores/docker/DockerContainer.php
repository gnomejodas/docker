<?php

namespace Spatie\Docker;

use Spatie\Docker\Exceptions\CouldNotStartDockerContainer;
use Spatie\Macroable\Macroable;
use Symfony\Component\Process\Process;

class DockerContainer
{
    use Macroable;

    public string $image = '';

    public string $name = '';

    public bool $daemonize = true;

    /**
     * @var \Spatie\Docker\PortMapping[]
     */
    public array $portMappings = [];

    public bool $cleanUpAfterExit = true;

    public bool $stopOnDestruct = false;
    
    public string $network = '';
    
    public bool $init = false;
    
    public string $volumeHost = '';
    
    public string $volumeContainer = '';

    public function __construct(string $image, string $name = '')
    {
        $this->image = $image;

        $this->name = $name;
    }

    public static function create(...$args): self
    {
        return new static(...$args);
    }

    public function image(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function daemonize(bool $daemonize = true): self
    {
        $this->daemonize = $daemonize;

        return $this;
    }

    public function doNotDaemonize(): self
    {
        $this->daemonize = false;

        return $this;
    }

    public function cleanUpAfterExit(bool $cleanUpAfterExit): self
    {
        $this->cleanUpAfterExit = $cleanUpAfterExit;

        return $this;
    }

    public function doNotCleanUpAfterExit(): self
    {
        $this->cleanUpAfterExit = false;

        return $this;
    }

    public function mapPort(int $portOnHost, $portOnDocker): self
    {
        $this->portMappings[] = new PortMapping($portOnHost, $portOnDocker);

        return $this;
    }
    
    public function network(string $network): self
    {
        
        $this->network = $network;
        
        return $this;
        
    }
    
    
    public function init(bool $init): self
    {
        
        $this->init = $init;
        
        return $this;
        
    }
    
        public function volume(string $hostFolder, string $containerFolder): self
    {
        
        $this->volumeHost = $hostFolder;
        
        $this->volumeContainer = $containerFolder;
        
        return $this;
        
    }
    
    public function stopOnDestruct(bool $stopOnDestruct = true): self
    {
        $this->stopOnDestruct = $stopOnDestruct;

        return $this;
    }

    public function getStartCommand(): string
    {
        return "docker run {$this->getExtraOptions()} {$this->image}";
    }

    public function start(): DockerContainerInstance
    {
        $command = $this->getStartCommand();

        $process = Process::fromShellCommandline($command);

        $process->run();

        if (! $process->isSuccessful()) {
            throw CouldNotStartDockerContainer::processFailed($this, $process);
        }

        $dockerIdentifier = $process->getOutput();

        return new DockerContainerInstance(
            $this,
            $dockerIdentifier,
            $this->name,
        );
    }

    protected function getExtraOptions(): string
    {
        $extraOptions = [];

        if (count($this->portMappings)) {
            $extraOptions[] = implode(' ', $this->portMappings);
        }

        if ($this->name !== '') {
            $extraOptions[] = "--name {$this->name}";
        }
        
        if ($this->network !== ''){
            $extraOptions[] = "--net {$this->network}";
        }
        
        if ($this->volumeHost !== '' && $this->volumeContainer !== ''){
            
            $extraOptions[] = "-t -d -v {$this->volumeHost}:{$this->volumeContainer}";
        }
        
        if ($this->init){
            
            $extraOptions[] = '--init';
        }

        if ($this->daemonize) {
            $extraOptions[] = '-d';
        }

        if ($this->cleanUpAfterExit) {
            $extraOptions[] = '--rm';
        }

        return implode(' ', $extraOptions);
    }
}
