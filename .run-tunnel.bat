@ECHO OFF
GOTO :RUN_TUNNEL

REM Change DNS to 8.8.8.8 and 8.8.4.4 both android and windows
REM Add firewall exception for both Inbound and Outbound ports: 80,443,22
REM use these commands:
REM ngrok http 8000
REM or
REM (example) ngrok http -host-header=rewrite localhost:PORT
REM ngrok http -host-header=rewrite localhost:8000
REM or
REM Use the static url given by ngrok (in ngrok account):
REM ngrok http --url=remarkably-patient-wildcat.ngrok-free.app 80
REM actual is:
REM ngrok http --url=remarkably-patient-wildcat.ngrok-free.app 8000
REM
REM Port must be the same as laravel port

:RUN_TUNNEL
ngrok http --region ap --url=remarkably-patient-wildcat.ngrok-free.app 8000
