
with open('day02.txt') as file:
    
    x,y = 0,0
    
    for line in file:
        action, amount = line.split(' ')
        if action == 'up':
            y -= int(amount)
        elif action == 'down':
            y += int(amount)
        elif action == 'forward':
            x += int(amount)

print(x,y,x*y)
