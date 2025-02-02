# Schizobene-3d-webgame
started as a chat.DeepSeek.com experiment :-)

because stupid tech reviews simply prompted `write the game "snake" in python` an existing game :-( https://youtu.be/bOsvI3HYHgI?si=hdbKXkB9Yqc6ijBj&t=160
or `write the game "Tetris" in python` for which there are hundreds of tetris clones in the training data: https://youtu.be/bOsvI3HYHgI?si=BizX1apY6EnxcTOk&t=379

So i decided to prompt DeepSeek for a new game:

first prompt [1 prompt deepseek.md](https://github.com/RoboDurden/Schizobene-3d-webgame/blob/main/deepseek%20prompts/1%20prompt%20deepseek.md)

**Create the code for a 3d web game with first-person viewpoint were objects on a 3x3 grid come flying towards the player and he has to move left or right or up or down on this 3x3 grid to avoid being hit. So the approaching objects appear very small and then become bigger and bigger until they nearly fill the full screen.\
The objects are either a cube, a pyramid or a ball. And in the color red or green or blue. So 9 different kind of possible objects, and the next object appears when the user has successfully avoided being hit by the current object.\
After 8 objects have appeared on random positions of the 3x3 grid, a fully populated grid appears with all the 9 possible objects. This time the player has not to avoid but has to hit that object that was missing from the 8 pervious objects. This then is one level.\
For each sequence, the 8 objects have random order and no dublicates. So there is always missing one kind. And this missing kind has to be hit in the final step of that level, when the 3x3 grid offers alle 9 kinds of objects.\
If the correct missing object is hit by the player, the next level begins with a faster speed.\
For every object successfully avoided a score counter increments. Hitting the missing object at the end of each level adds 2 to the counter. So the player gains 10 points for every level.\
The player has three lives. He loses a life when being hit by an objects or if he chooses the wrong missing object at the end of a level.\
If he still has a life, the current level restarts. Otherwise it is game over and he can enter his name in a top-ten list if his socre is high enough.\
Then hitting a key restarts the game with 5 levels lower than the one he just lost. Or level 0 if he had not reached level 6 or higher.**

https://github.com/user-attachments/assets/de43f026-4d9d-4a6f-9d80-62a022eef65a

second rewritten prompt [2 prompt deepseek.md](https://github.com/RoboDurden/Schizobene-3d-webgame/blob/main/deepseek%20prompts/2%20prompt%20deepseek.md)

**Create the code for a 3d web game with first-person viewpoint.\
For each level, the player has to fly through a tunnel where he can move sideways (x = left, right and z = up, down) with the arrow keys on a 3x3 grid. The length of the tunnel is 18 units. The player starts at y=0.\
At every  y=1, 3, 5, 7, 9, 11, 13 and 15 there are 8 objects placed on the 3x3 grid so that there is only one way to fly past the obstacles. The objects should be so big that they fill 90% of their grid position. So the 8 objects act like a wall with only the empty position to fly through.\
As it is a first-person viewpoint game, when the player moves one unit to the side, all the objects are seen to shift in the opposite direction.\
And as the player flys through the tunnel, the objects get bigger in size.\
The objects are either a cube, a pyramid or a ball. And in the color red or green or blue. So 9 different kind of possible objects.\
Each wall is only made of one kind of these 9 different possible objects.\
To set the objects for these walls, make a list of the nine possible objects and shuffle them by random. Then the first 8 objects in the list set the objects for the 8 walls.\
At the end of the tunnel at y=17, there is a ninth wall. This wall is made up of all 9 possible objects placed randomly on the 3x3 grid.\
And this time, the player has to move sideways to be in that x-z position that will make him collide with the ninths object in the list. This of course is the object that no wall has yet been made of. When he collides with this special object, it disapears and the player flys through the ninth wall and completes the level, and the next level/tunnel follows with a faster speed.\
For every wall passed a score counter increments. Passing the ninth wall adds 2 to the counter. So the player gains 10 points for every level/tunnel.\
The player has three lives. He loses a life when he does not shift sideways to the only position that allows him to pass a wall. If he then still has a life, the current level restarts after a hit on the keyboard. Otherwise it is game over and he can enter his name in a top-ten list if his socre is high enough.\
After which hitting a key restarts the game with 5 levels lower than the one he just lost. Or level 0 if he had not reached level 6 or higher.**


https://github.com/user-attachments/assets/8c194498-53c4-4f55-a817-0678119dcba3

and then i had to fix bugs and add features for another few days !

## You can play the final game here: https://www.SchizoBene.de/game

https://github.com/user-attachments/assets/179a5165-cf71-405e-b728-8cf40ac71e1e

https://github.com/user-attachments/assets/b4a4e79d-773d-4b48-95f3-b2934a3657cd

this video from 2025/2/2:  [the Schizo-bene online game.mp4](https://github.com/RoboDurden/Schizobene-3d-webgame/raw/refs/heads/main/mp4/the%20Schizo-bene%20online%20game.mp4)


