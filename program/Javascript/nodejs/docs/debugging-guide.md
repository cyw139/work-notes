# Debugging Guide
This guide will help you get started debugging your Node.js apps and scripts.
## 1、Enable Inspector
- [ ] When started with the `--inspect` switch, a Node.js process listens for a debugging client. By default, it will listen at host and port 127.0.0.1:9229. Each process is also assigned a unique [UUID](https://tools.ietf.org/html/rfc4122).
- [ ] Inspector clients must know and specify host address, port, and UUID to connect. A full URL will look something like `ws://127.0.0.1:9229/0f2c936f-b1cd-4ac9-aab3-f63b0f33d55e`.
- [ ] Node.js will also start listening for debugging messages if it receives a `SIGUSR1` signal. (`SIGUSR1` is not available on Windows.) In Node.js 7 and earlier, this activates the legacy Debugger API. In Node.js 8 and later, it will activate the Inspector API.