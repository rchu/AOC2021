
with open('day02.txt') as file:
    
    x,y = 0,0
    aim = 0
    for line in file:
        action, amount = line.split(' ')
        if action == 'up':
            aim -= int(amount)
        elif action == 'down':
            aim += int(amount)
        elif action == 'forward':
            x += int(amount)
            y += int(amount) * aim

print(x,y,x*y)
