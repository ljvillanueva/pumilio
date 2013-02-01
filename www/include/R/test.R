#Reduce output
options(echo = FALSE)

#Libraries
if (!try(require(tuneR)))
        q(save = "no", status = 91, runLast = FALSE)

if (!try(require(seewave)))
        q(save = "no", status = 92, runLast = FALSE)

if (!try(require(RMySQL)))
        q(save = "no", status = 93, runLast = FALSE)

if (!try(require(ineq)))
        q(save = "no", status = 94, runLast = FALSE)
        
q(save = "no", status = 0, runLast = FALSE)

