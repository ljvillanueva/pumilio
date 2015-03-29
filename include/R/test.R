#Reduce output
options(echo = FALSE)

#Libraries
if (!try(suppressMessages(require(tuneR))))
        q(save = "no", status = 91, runLast = FALSE)

if (!try(suppressMessages(require(seewave))))
        q(save = "no", status = 92, runLast = FALSE)

if (!try(suppressMessages(require(RMySQL))))
        q(save = "no", status = 93, runLast = FALSE)

if (!try(suppressMessages(require(ineq))))
        q(save = "no", status = 94, runLast = FALSE)
        
q(save = "no", status = 0, runLast = FALSE)

