<?php

namespace SunnyFlail\Router;

use RuntimeException;

/**
 * Simple debbuging tool for Router
 */
final class RouterDebbuger
{

    private Router $router;
    private array $issues;
    private int $issueCount;
    private bool $debugged;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->issues = [];
        $this->issueCount = 0;
        $this->debugged = false;
    }

    /**
     * Runs the debugger
     * 
     * It performs a mutation of this instance of RouterDebugger,
     * adding found Issues to @see $issues, updating the @see $issueCount
     * and finally setting @see $debugged to true
     * 
     * Results may be fetched with @method RouterDebugger::getFoundIssues
     * 
     * @return bool true if there was an issue,
     *              false if there were no issues
     */
    public function run(): bool
    {
        if ($this->debugged) {
            throw new RuntimeException("This instance of debugger already run!");
        }

        $routes = $this->router->getAllRoutes();
        $defaultedUrls = [];

        foreach ($routes as $index => $route) {
            $name = $route->getName();
            $path = $route->getPath();
            $defaults = $route->getDefaults();
            $params = $route->getParams();
            foreach ($defaults as $defName => $defValue) {
                $path = str_replace("{".$defName."}", "", $path);
                if (!isset($params[$defName])) {
                    $this->addIssue(
                        "Defaults provided for non existing Params",
                        $name,
                        $defName
                    );
                }
                if (!preg_match("/".$params[$defName]."/i", $defValue)) {
                    $this->addIssue(
                        "Defaults not matching param requirements",
                        $name,
                        [
                            "Expected" => $params[$defName],
                            "Got" => $defValue
                        ]
                    );
                }
            }

            $tempPath = $path;
            while (preg_match("/\/\/\{(\w+)\}/i", $tempPath, $matched)) {
                $this->addIssue(
                    "Params without default value placed after params with default",
                    $name,
                    $matched[1]
                );
                $tempPath = str_replace($matched[0], "", $tempPath);
            }

            $path = strtolower(str_replace("/", "", $path));
            if (array_key_exists($name, $defaultedUrls)) {
                if (!$this->hasIssue("Mutiple Routes with same name", $name)) {
                    $key = $this->indexOf($path, $defaultedUrls);
                    $oldRoute = $this->router->getRoute($key);
                    $this->addIssue(
                        "Mutiple Routes with same name",
                        $name,
                        $oldRoute->jsonSerialize()
                    );   
                }
                $this->addIssue(
                    "Mutiple Routes with same name",
                    $name,
                    $route->jsonSerialize()
                );
            }

            if (in_array($path, $defaultedUrls)) {
                if (!$this->hasIssue("Multiple routes matching same path", $name)) {
                    $key = $this->indexOf($path, $defaultedUrls);
                    $oldRoute = $this->router->getRoute($key);
                    $this->addIssue(
                        "Multiple routes matching same path",
                        $path,
                        $oldRoute->jsonSerialize()
                    );   
                }
                $this->addIssue(
                    "Multiple routes matching same path",
                    $path,
                    $route->jsonSerialize()
                );
            }
            $defaultedUrls[$index] = $path;
        }

        return $this->issueCount >= 0;
    }

    /**
     * Checks whether there are cases of issue with provided name and identifier
     * 
     * @param string $issueName Name of the issue whose case is looked for
     * @param string $identifier Name of the issue whose case is looked for
     * 
     * @return bool
     */
    private function hasIssue(string $issueName, string $identifier): bool
    {
        return isset($this->issues[$issueName]) && isset($this->issues[$issueName][$identifier]);
    }

    /**
     * Adds new issue to the collection and updates the Issue Counter
     * 
     * @param string $issueName Name of the issue to which this value is appended to
     * @param string $identifier Name of the issue case this value  is appended to
     * @param mixed $value Body of the issue - may be array, string, Object
     * 
     * @return void
     */
    private function addIssue(string $issueName, string $identifier, $value)
    {
        if (!isset($this->issues[$issueName])) {
            $this->issues[$issueName] = [];
        }
        if (!isset($this->issues[$issueName][$identifier])) {
            $this->issues[$issueName][$identifier] = [];
        }
        $this->issues[$issueName][$identifier][] = $value;
        $this->issueCount ++;
    }

    /**
     * Returns key at which needle exists in haystack
     * 
     * @param mixed $needle Value searched for
     * @param array $haystack Array which is searched against
     * 
     * @return string Key or stringified index if found
     * @return null If $needle doesn't exist in $haystack
     */
    private function indexOf($needle, array $haystack): ?string
    {
        if (!in_array($needle, $haystack)) {
            return null;
        }
        foreach ($haystack as $key => $value) {
            if ($value === $needle) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Returns array containing representation of found issues
     * 
     * @return int
     */
    public function getFoundIssues(): array
    {
        if (!$this->debugged) {
            throw new \RuntimeException("Can't get found issues before running debugger!");
        }
        return $this->issues;
    }

    /**
     * Returns the number of issues found during debugging
     * 
     * @return int
     */
    public function getIssueCount(): int
    {
        if (!$this->debugged) {
            throw new RuntimeException("Can't get found issues before running debugger!");
        }
        return $this->issueCount;
    }

}