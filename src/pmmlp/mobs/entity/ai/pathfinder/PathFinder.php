<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\pathfinder;

use pmmlp\mobs\entity\Mob;
use pmmlp\mobs\util\MobsConfig;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;

class PathFinder {
    /** @var Node[]  */
    protected array $openList = [];
    /** @var Node[]  */
    protected array $closedList = [];

    protected ?Node $targetNode = null;
    protected ?Node $bestNode = null;

    public function __construct(
        protected Mob $mob
    ){}

    public function findPath(Vector3 $target): ?Path {
        $mob = $this->mob;
        $start = $mob->getPosition();

        $evaluator = $mob->getNodeEvaluator();

        $world = $this->mob->getWorld();

        if(!$world->isInWorld($start->getFloorX(), $start->getFloorY(), $start->getFloorZ())) {
            return null;
        }

        $startNode = Node::fromVector3($start);
        $startNode->setG(0.0);
        $startNode->setH($this->calculateHCost($start, $target));

        $this->targetNode = Node::fromVector3($target);

        $this->openList[$startNode->getHash()] = $startNode;

        for($i = 0; $i <= MobsConfig::$maxPathfinderIterations; $i++) {
            $key = $this->getLowestFCost();
            if($key === null){
                break;
            }
            $currentNode = $this->openList[$key];

            unset($this->openList[$currentNode->getHash()]);
            $this->closedList[$currentNode->getHash()] = $currentNode;

            if($currentNode->getHash() === $this->targetNode->getHash()) {//Path found
                $this->targetNode->setParentNode($currentNode);
                break;
            }

            $sides = [];
            foreach(Facing::HORIZONTAL as $sideId) {
                $sideVector = $currentNode->getSide($sideId);
                $y = $sideVector->getFloorY();
                $side = $evaluator->evaluate($currentNode, $sideVector);
                if($side === null) {
                    continue;
                }
                if($sideVector->getFloorY() === $y) {
                    $sides[] = $sideId;
                }
                $this->handleSide($side, $currentNode, $target);
            }

            foreach(Facing::HORIZONTAL as $side) {
                $clockwiseRotateSide = Facing::rotateY($side, true);
                $sideVector = $currentNode->getSide($side)->getSide($clockwiseRotateSide);
                if(in_array($side, $sides, false) && in_array($clockwiseRotateSide, $sides, false)) {
                    $side = $evaluator->evaluate($currentNode, $sideVector);
                    if($side === null) {
                        continue;
                    }
                    $this->handleSide($side, $currentNode, $target);
                }
            }
        }
        return $this->finish($target);
    }

    private function handleSide(Vector3 $side, Node $currentNode, Vector3 $target): void {
        $world = $this->mob->getWorld();

        $sideNode = Node::fromVector3($side);
        if(isset($this->closedList[$sideNode->getHash()])) {
            return;
        }

        $cost = $world->getBlock($sideNode)->isSameType(VanillaBlocks::GRASS()) ? 0 : 2;//TODO: Add block cost
        if($currentNode->getFloorY() !== $sideNode->getFloorY()) {
            $cost += 2;
        }
        $g = $currentNode->getG() + $cost;
        if(!isset($this->openList[$sideNode->getHash()]) || $g < $sideNode->getG()) {
            $sideNode->setG($g);
            $sideNode->setH($this->calculateHCost($side, $target));
            $sideNode->setParentNode($currentNode);
            if(!isset($this->openList[$sideNode->getHash()])) {
                $this->openList[$sideNode->getHash()] = $sideNode;
            }
            if($this->bestNode === null || $this->bestNode->getH() > $sideNode->getH()) {
                $this->bestNode = $sideNode;
            }
        }
    }

    protected function finish(Vector3 $target): ?Path{
        $node = $this->targetNode?->getParentNode();
        if($node === null){
            $node = $this->bestNode;
            if($node === null) {
                $this->reset();
                return null;
            }
        }
        $pathResult = new Path($this->mob->getPosition(), $target);
        if($node->getHash() === $this->targetNode->getParentNode()?->getHash()) {
            $pathResult->addPathPoint(new Vector3($target->getFloorX() + 0.5, $target->y, $target->getFloorZ() + 0.5));
        }
        while(true) {
            $node = $node->getParentNode();
            if($node instanceof Node) {
                $pathResult->addPathPoint(new Vector3($node->x, $node->y, $node->z));
                continue;
            }
            break;
        }
        $this->reset();
        return $pathResult;
    }

    private function reset(): void {
        $this->openList = [];
        $this->closedList = [];
        $this->targetNode = null;
        $this->bestNode = null;
    }

    protected function getLowestFCost(): ?int {
        $openList = [];
        foreach($this->openList as $hash => $node) {
            $openList[$hash] = $node->getF();
        }
        asort($openList);
        return array_key_first($openList);
    }

    protected function calculateHCost(Vector3 $vector3, Vector3 $target): float{
        return abs($vector3->x - $target->x) + abs($vector3->y - $target->y) + abs($vector3->z - $target->z);
    }
}