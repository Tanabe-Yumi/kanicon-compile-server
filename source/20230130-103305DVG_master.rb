led26 = GPIO.new( 26, GPIO::OUT )
loop do
  led26.write(1)
  sleep(1)
  led26.write(0)
  sleep(1)
end
